# Microsoft Edge 性能警告解决方案

## 问题分析

您在 Microsoft Edge 浏览器中看到的警告主要包括：

### 1. `document.write()` 警告
- **问题来源**: ECharts 库或其他第三方库使用了过时的 `document.write()` 方法
- **影响**: 阻塞 DOM 解析，影响页面加载性能
- **严重程度**: 中等

### 2. 非被动事件监听器警告 (主要问题)
- **问题来源**: ECharts 为触摸和滚轮事件添加了非被动监听器
- **相关事件**:
  - `touchstart` - 触摸开始
  - `touchmove` - 触摸移动  
  - `mousewheel` - 鼠标滚轮
- **影响**: 阻塞页面滚动，降低用户体验
- **严重程度**: 高

## 解决方案实施

### 1. 被动事件监听器修复 ✅
创建了 `echarts-passive-fix.js` 文件，通过以下方式解决：

```javascript
// 重写 addEventListener 方法
EventTarget.prototype.addEventListener = function(type, listener, options) {
    // 对滚动相关事件自动设置 passive: true
    if (['touchstart', 'touchmove', 'mousewheel', 'wheel'].indexOf(type) !== -1) {
        if (options.passive === undefined) {
            options.passive = true;
        }
    }
    return originalAddEventListener.call(this, type, listener, options);
};
```

### 2. 应用层事件监听器优化 ✅
优化了 `kline.js` 中的事件监听器：

```javascript
// window resize 事件 - 添加防抖和被动模式
window.addEventListener('resize', resizeHandler, { passive: true });

// message 事件 - 使用被动模式
window.addEventListener('message', messageHandler, { passive: true });
```

### 3. 性能优化措施 ✅

#### 防抖技术
```javascript
let resizeTimer = null;
const resizeHandler = () => {
    if (resizeTimer) clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        if (myChart && !myChart.isDisposed()) {
            myChart.resize();
        }
    }, 100);
};
```

#### 错误处理
```javascript
try {
    window.addEventListener('resize', resizeHandler, { passive: true });
} catch (e) {
    // 降级到传统模式
    window.addEventListener('resize', resizeHandler, false);
}
```

## 验证方法

1. **清除浏览器缓存**
2. **重新加载页面**  
3. **打开开发者工具 Console 面板**
4. **检查是否还有 Violation 警告**

## 预期结果

实施这些修复后，您应该看到：

- ✅ 大幅减少或消除 `touchstart/touchmove/mousewheel` 相关警告
- ✅ 提升页面滚动流畅性
- ✅ 减少主线程阻塞
- ✅ 改善整体用户体验

## 技术原理

### 被动事件监听器
被动事件监听器通过 `{ passive: true }` 告诉浏览器：
- 事件处理函数不会调用 `preventDefault()`
- 浏览器可以立即执行默认行为（如滚动）
- 不需要等待事件处理函数执行完成

### 防抖技术
防止高频事件（如 resize）过度触发：
- 延迟执行函数
- 在延迟期间如果再次触发，重置计时器
- 只在最后一次触发后执行

## 注意事项

1. **ECharts 版本**: 建议升级到最新版本，新版本已优化了事件监听器
2. **兼容性**: 修复脚本包含了降级处理，确保在不支持被动事件的浏览器中正常工作
3. **监控**: 持续关注控制台输出，确保无新的性能警告

## 进一步优化建议

1. **升级 ECharts**: 考虑升级到 ECharts 5.x 最新版本
2. **CDN 优化**: 使用 CDN 版本的 ECharts 以获得更好的缓存效果
3. **按需加载**: 只引入需要的 ECharts 模块，减小包体积
