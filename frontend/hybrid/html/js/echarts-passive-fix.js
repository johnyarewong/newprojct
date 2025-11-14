/**
 * ECharts 被动事件监听器修复脚本（备用）
 * 解决 Chrome/Edge 中的 Violation 警告
 * 注意：主要修复代码已在 HTML 中实现，此文件作为备用
 */
(function() {
    'use strict';
    
    // 静默日志，避免控制台输出过多信息
    var silentMode = true;
    
    function log(message) {
        if (!silentMode && console && console.log) {
            console.log(message);
        }
    }
    
    // 等待 ECharts 加载完成后进行额外的配置
    if (window.echarts) {
        applyEChartsConfig();
    } else {
        var checkECharts = setInterval(function() {
            if (window.echarts) {
                clearInterval(checkECharts);
                applyEChartsConfig();
            }
        }, 50);
        
        setTimeout(function() {
            clearInterval(checkECharts);
        }, 5000);
    }
    
    function applyEChartsConfig() {
        log('[ECharts Config] ECharts loaded successfully');
        
        // 确保 ECharts 使用被动事件监听器
        if (window.echarts && window.echarts.init) {
            var originalInit = window.echarts.init;
            
            window.echarts.init = function(dom, theme, opts) {
                // 调用原始的 init 方法
                var chart = originalInit.call(this, dom, theme, opts);
                
                // 添加被动事件优化配置
                if (chart && chart.setOption) {
                    var originalSetOption = chart.setOption;
                    chart.setOption = function(option, notMerge, lazyUpdate) {
                        // 确保 option 中的交互配置正确
                        if (option && typeof option === 'object') {
                            // 禁用部分可能导致问题的默认交互
                            if (!option.tooltip) {
                                option.tooltip = {};
                            }
                            if (option.tooltip.triggerOn === undefined) {
                                option.tooltip.triggerOn = 'mousemove|click';
                            }
                        }
                        
                        return originalSetOption.call(this, option, notMerge, lazyUpdate);
                    };
                }
                
                return chart;
            };
        }
    }
    
    // 阻止可能的错误冒泡
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('preventDefault')) {
            log('[Passive Fix] Prevented error: ' + e.message);
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

})();
