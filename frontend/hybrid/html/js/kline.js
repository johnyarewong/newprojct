var myChart;

// post请求封装
// function post(url,data) {
// 	let baseUrl='http://192.168.3.135:8080';
// 	return new Promise((resolve,reject)=>{
// 		axios({
// 			headers:{
// 				"Content-Type": "application/x-www-form-urlencoded",
// 			},
// 			method:'post',
// 			url:baseUrl+url,
// 			data:Qs.stringify( data || {})
// 		})
// 		.then(res=>{
// 			处理响应数据
// 			if(res.data.code==1){
// 				resolve(res.data)
// 			}else{
// 				reject()
// 				alertError('请求超时')
// 			}
// 		})
// 		.catch(err=>{
// 			alertError('请求超时')
// 		})
// 	})
// }
// 弹窗
function alertError(title) {
	document.addEventListener('plusready', function() {
		// Document ready
	})
	try {
		plus.nativeUI.toast(title, {
			icon: '/static/common/toast-error.png',
			style: 'inline',
			verticalAlign: 'top'
		});
	} catch (e) {
		//TODO handle the exception
	}
}
// 语言包配置
const i18nMessages = {
	'zh-Hans': {
		kline_loading: '数据加载中...'
	},
	'zh-Hant': {
		kline_loading: '數據加載中...'
	},
	'en': {
		kline_loading: 'Loading data...'
	},
	'ja': {
		kline_loading: 'データ読み込み中...'
	},
	'th': {
		kline_loading: 'กำลังโหลดข้อมูล...'
	},
	'hi': {
		kline_loading: 'डेटा लोड हो रहा है...'
	},
	'ms': {
		kline_loading: 'Memuat data...'
	},
	'ru': {
		kline_loading: 'Загрузка данных...'
	},
	'ko': {
		kline_loading: '데이터 로딩 중...'
	}
};

// 获取URL参数的函数
function getUrlParam(name) {
	const urlParams = new URLSearchParams(window.location.search);
	return urlParams.get(name);
}

// 获取当前语言，默认为中文
function getCurrentLocale() {
	return getUrlParam('locale') || 'zh-Hans';
}

// 获取翻译文本的函数
function $t(key) {
	const locale = getCurrentLocale();
	return (i18nMessages[locale] && i18nMessages[locale][key]) || i18nMessages['zh-Hans'][key] || key;
}

// 禁用Vue开发工具和警告信息
if (typeof Vue !== 'undefined' && Vue.config) {
	Vue.config.productionTip = false;
	Vue.config.devtools = false;
	Vue.config.debug = false;
	Vue.config.silent = true;
	Vue.config.performance = false;
	Vue.config.errorHandler = function(err, vm, info) {
		// 静默处理Vue错误，特别是DevTools相关错误
		if (err && err.message && (err.message.includes('DevtoolsBackend') || err.message.includes('emit is not a function'))) {
			return;
		}
		console.error('Vue Error:', err, info);
	};
}

// 过滤Vue相关的控制台输出
const originalConsoleWarn = console.warn;
const originalConsoleLog = console.log;
const originalConsoleInfo = console.info;

console.warn = function(...args) {
	const message = args.join(' ');
	// 过滤Vue开发工具相关的警告
	if (message.includes('Download the Vue Devtools') || 
		message.includes('vue-devtools') || 
		message.includes('[Vue') ||
		message.includes('Vue warn') ||
		message.includes('DevtoolsBackend') ||
		message.includes('emit is not a function')) {
		return;
	}
	originalConsoleWarn.apply(console, args);
};

console.log = function(...args) {
	const message = args.join(' ');
	// 过滤Vue相关的日志
	if (message.includes('Download the Vue Devtools') || 
		message.includes('vue-devtools') ||
		message.includes('You are running Vue in development mode') ||
		message.includes('DevtoolsBackend') ||
		message.includes('emit is not a function')) {
		return;
	}
	originalConsoleLog.apply(console, args);
};

console.info = function(...args) {
	const message = args.join(' ');
	// 过滤Vue相关的信息
	if (message.includes('Download the Vue Devtools') || 
		message.includes('vue-devtools') ||
		message.includes('Vue') ||
		message.includes('DevtoolsBackend') ||
		message.includes('emit is not a function')) {
		return;
	}
	originalConsoleInfo.apply(console, args);
};

// 全局错误处理 - 防止未捕获的错误影响页面运行
window.addEventListener('unhandledrejection', function(event) {
	if (event.reason && event.reason.message && 
		(event.reason.message.includes('DevtoolsBackend') || 
		 event.reason.message.includes('emit is not a function'))) {
		event.preventDefault();
		console.log('已阻止DevTools相关的Promise错误');
	}
});

// 重写console.error来处理更多Vue DevTools错误
const originalConsoleError = console.error;
console.error = function(...args) {
	const message = args.join(' ');
	if (message.includes('DevtoolsBackend') || 
		message.includes('emit is not a function') ||
		message.includes('vue-devtools')) {
		return; // 静默处理这些错误
	}
	originalConsoleError.apply(console, args);
};

var app = new Vue({
	el: '#app',
	data: {
		MA5: null,
		MA10: null,
		MA30: null,
		volMA5: null,
		volMA10: null,
		current: 5,
		tabs: [{
				'label': '5M',
				'value': 5
			},
			{
				'label': '15M',
				'value': 15
			},
			{
				'label': '30M',
				'value': 30
			},
			{
				'label': '1H',
				'value': 60
			},
			{
				'label': '1D',
				'value': "d"
			},
		],
		category: 1,
		categoryList: [{
				'label': '深度',
				'value': 1
			},
			{
				'label': '成交',
				'value': 2
			},
			{
				'label': '简介',
				'value': 3
			},
		],
		txData: {
			lastPrice: null,
			volume: null,
			low: null,
			high: null,
			upFlag: 1
		}, //交易数据统计
		loadingText: '数据加载中...', // 加载文本，将在created中初始化
		buyList: [],
		sellList: [],
		dealHis: [],
		tokenInfo: {},
		page: 1,
		url: "https://admin.xxxxxx.com",
		timer: "",
		new_value: 1938.90,
		vue_1: "",
		vue_2: "",
		vue_11: "",
		vue_22: "",
		vue_3_1: "",
		vue_3_2: "",
		vue_3_3: "",
		vue_4_1: "",
		vue_4_2: "",
		vue_4_3: "",
		vue_4_4: "",
		newsData: [
			'比特币突破50000美元大关，市场情绪高涨',
			'以太坊2.0质押量超过1000万枚ETH',
			'美国SEC批准首只比特币现货ETF',
			'币安宣布推出新的DeFi流动性挖矿项目',
			'特斯拉CEO马斯克再次发推支持狗狗币',
			'MicroStrategy再次增持5000枚比特币',
			'NFT市场交易量创历史新高',
			'日本计划将加密货币纳入养老金投资组合',
			'Coinbase宣布支持更多主流加密货币交易',
			'Solana生态TVL突破100亿美元',
			'央行数字货币CBDC试点范围扩大',
			'Layer2解决方案Arbitrum用户数突破百万',
			'加密货币总市值重返2万亿美元',
			'Polygon与迪士尼达成合作协议',
			'比特币闪电网络容量创历史新高',
			'灰度比特币信托转换为现货ETF获批',
			'Web3社交平台用户数突破1000万',
			'DeFi协议总锁仓量TVL突破500亿美元',
			'欧盟通过MiCA加密资产监管法案',
			'区块链游戏月活跃用户突破200万'
		],
		visibleNews: [],
		newsIndex: 0,
		newsTimer: null,
	},
	created() {
		// 初始化加载文本
		this.loadingText = $t('kline_loading');
		
		this.getTxData()
		this.getDepth()
		let this_ = this;
		this.timer = self.setInterval(function() {
			this_.getTxData();
		}, 2000);
		
		// 初始化新闻滚动
		this.initNewsScroll();
		
		// 监听URL变化（语言切换）
		this.watchUrlChange();
	},
	mounted() {
		let this_ = this;
		myChart = echarts.init(document.getElementById('main'));
		this_.draw()
		this_.getKline()
	},
	methods: {
		// 初始化新闻滚动
		initNewsScroll() {
			// 初始显示第一条新闻
			this.visibleNews = [this.newsData[0]];
			this.newsIndex = 0;
			
			// 每3秒切换一次新闻
			let this_ = this;
			this.newsTimer = setInterval(function() {
				this_.scrollNews();
			}, 3000);
		},
		// 新闻滚动
		scrollNews() {
			this.newsIndex = (this.newsIndex + 1) % this.newsData.length;
			this.visibleNews = [this.newsData[this.newsIndex]];
		},
		// 买入
		back1() {

		},
		// 卖出
		back2() {

		},
		getUrlCode(name) {
			// 改进URL参数解析
			const urlParams = new URLSearchParams(window.location.search);
			const idParam = urlParams.get('id') || window.location.search.split("id")[1];
			return idParam;
		},
		// 获取24小时交易数据统计
		getTxData() {
			let this_ = this;
			var shijian = new Date().getTime();
			axios.get(this.url + '/index/api/getprodata?pid=' + this.getUrlCode("id") + '&_=' + shijian).then(
				res => {
					var decryptStr = window.atob(res.data);
					var result = JSON.parse(decryptStr);
					var a = 0;
					var lastPrice = this_.txData.lastPrice || 0;
					if (result.now > lastPrice) {
						a = 2
					} else if (result.now < lastPrice) {
						a = 1
					} else {
						a = 1
					}
					this_.txData = {
						// 最新成交价
						"lastPrice": result.now,
						// 涨幅
						"upRate": result.now,
						// 1跌绿 2涨红
						"upFlag": a,
						// 24小时交易量
						"volume": result.open,
						// 24小时最高价
						"high": result.highest,
						// 24小时最低价
						"low": result.lowest,
					}
					this_.new_value = result.now;
					var options = myChart.getOption(); 
					options.series[0].markLine.data[0].yAxis = this_.new_value; 
					myChart.setOption(options);
				}).catch(error => {
					// API请求失败时使用模拟数据
					this_.txData = {
						lastPrice: 239.62,
						upRate: "+2.34%", 
						upFlag: 2,
						volume: 125.89,
						high: 245.12,
						low: 234.56
					};
					this_.new_value = 239.62;
				});
		},
		time_(timestamp) {
		    // 将时间戳转换为毫秒级别
		    const date = new Date(timestamp * 1000);
		    // 获取年、月、日
		    const year = date.getFullYear();
		    const month = ('0' + (date.getMonth() + 1)).slice(-2);
		    const day = ('0' + date.getDate()).slice(-2);
		    // 拼接日期格式
		    const formattedDate = `${year}-${month}-${day}`;
		    return formattedDate;
		},
		// 获取k线数据,生成k线
		getKline() {
			let this_ = this;
			var rawData = [
				["2004-01-05", 10411.85, 10544.07, 10411.85, 10575.92, 221290000],
			];
			var shijian = new Date().getTime();
							axios.get(this.url + '/index/api/getkdata?pid=' + this.getUrlCode("id") + '&num=60&interval=' + this
					.current).then(res => {
					// 检查响应数据是否为空
					if (!res.data || res.data.trim() === '') {
						this_.generateMockData();
						return;
					}
					
					var decryptStr;
					var result;
					
					try {
						decryptStr = window.atob(res.data);
					} catch (decodeError) {
						decryptStr = res.data; // 直接使用原数据
					}
					
					try {
						result = JSON.parse(decryptStr);
					} catch (parseError) {
						this_.generateMockData();
						return;
					}
					
					// 检查数据有效性
					if (!result) {
						this_.generateMockData();
						return;
					}
					
					if (!result.items) {
						this_.generateMockData();
						return;
					}
					
					if (!Array.isArray(result.items)) {
						this_.generateMockData();
						return;
					}
					
					if (result.items.length === 0) {
						this_.generateMockData();
						return;
					}
				
				// 处理API返回的真实数据
				for(var i=0;i<result.items.length;i++){
					result.items[i][0] = this_.time_(result.items[i][0]);
					result.items[i][5] = result.items[i][1];
				}
				rawData = result.items;
				this_.renderKlineChart(rawData);
				
			}).catch(error => {
				this_.generateMockData();
			});
		},
		
		// 监听URL变化（语言切换）
		watchUrlChange() {
			let this_ = this;
			// 如果父窗口发送消息（语言切换），更新加载文本
			// 使用被动事件监听器优化性能
			const messageHandler = function(event) {
				if (event.data && event.data.type === 'localeChange') {
					this_.loadingText = $t('kline_loading');
					this_.$forceUpdate();
				}
			};
			
			try {
				window.addEventListener('message', messageHandler, { passive: true });
			} catch (e) {
				// 降级到传统模式
				window.addEventListener('message', messageHandler, false);
			}
			
			// 页面初始化时根据URL参数更新语言
			this.updateLoadingText();
		},
		
		// 更新加载文本
		updateLoadingText() {
			this.loadingText = $t('kline_loading');
		},
		
		// 生成模拟K线数据
		generateMockData() {
			var mockData = [];
			var basePrice = 239.62; // BCH/USD 基础价格
			var currentTime = Math.floor(Date.now() / 1000);
			var interval = this.current === 'd' ? 86400 : this.current * 60; // 转换为秒
			
			// 生成60条数据
			for (var i = 59; i >= 0; i--) {
				var timestamp = currentTime - (i * interval);
				var open = basePrice + (Math.random() - 0.5) * 10;
				var variation = (Math.random() - 0.5) * 5;
				var high = open + Math.abs(variation) + Math.random() * 3;
				var low = open - Math.abs(variation) - Math.random() * 3;
				var close = open + variation;
				var volume = Math.floor(Math.random() * 1000000) + 100000;
				
				// 格式化时间
				var formattedTime = this.time_(timestamp);
				mockData.push([formattedTime, open, close, low, high, volume]);
			}
			
			this.renderKlineChart(mockData);
		},
		
		// 渲染K线图表
		renderKlineChart(rawData) {
			
			var dates = rawData.map(function(item) {
				return item[0];
			});
			var data = rawData.map(function(item) {
				return [+item[1], +item[2], +item[3], +item[4], +item[5]];
			});
			var volumes = rawData.map(function(item, index) {
				return [index, item[5], item[1] > item[2] ? 1 : -1];
			});
			var dataMA5 = this.calculateMA(5, data);
			var dataMA10 = this.calculateMA(10, data);
			var dataMA30 = this.calculateMA(30, data);
			var volumeMA5 = this.calculateMA(5, volumes);
			var volumeMA10 = this.calculateMA(10, volumes);
			

			
			// 更新图表
			if (myChart) {
				myChart.setOption({
					xAxis: [{
							data: dates
						},
						{
							data: dates
						},
					],
					series: [{
							name: '日K',
							data: data
						},
						{
							name: 'MA5',
							data: dataMA5
						},
						{
							name: 'MA10',
							data: dataMA10
						},
						{
							name: 'MA30',
							data: dataMA30
						},
						{
							name: 'Volume',
							data: volumes
						},
						{
							name: 'VolumeMA5',
							data: volumeMA5
						},
						{
							name: 'VolumeMA10',
							data: volumeMA10
						}
					]
				});
			}
		},
		
		// 列表条数不足补全
		addItem(list, type) {
			// type: 1开头加，2末尾加
			list = list || [];
			let len = 20 - list.length;
			if (len > 0) {
				for (let i = 0; i < len; i++) {
					if (type == 1) {
						list.unshift({})
					} else {
						list.push({})
					}
				}
			}
			return list;
		},
		// 获取深度数据
		getDepth() {
			this.buyList = this.addItem(depthList().buyList || []);
			this.sellList = this.addItem(depthList().sellList || []);
		},
		// 获取成交记录
		getDealHis() {
			this.dealHis = dealHis();
		},
		// 获取项目简介信息
		getTokenInfo() {
			this.tokenInfo = tokenInfo;
		},

		// 切换tab
		switchTab(val) {
			if (this.current == val) return;
			this.current = val;
			this.getKline()
		},
		// 切换类目
		switchCategory(val) {
			if (this.category == val) return;
			this.category = val;
			if (this.category == 1) {
				this.getDepth()
			} else if (this.category == 2) {
				this.getDealHis()
			} else {
				this.getTokenInfo()
			}
		},
		// 截取数字字符串 保留precision小数
		formatterNum(value, precision) {

			let reg = new RegExp('^\\d+(?:\\.\\d{0,' + precision + '})?')
			return value.toString().match(reg)
		},
		// 计算MA
		calculateMA(dayCount, data) {
			var result = [];
			for (var i = 0, len = data.length; i < len; i++) {
				if (i < dayCount) {
					result.push('-');
					continue;
				}
				var sum = 0;
				for (var j = 0; j < dayCount; j++) {
					sum += data[i - j][1];
				}

				result.push((sum / dayCount).toFixed(2));
			}
			return result;
		},
		// 绘制(配置项)
		draw() {
			let that = this;
			var upColor = '#03ad91';
			var downColor = '#dd345b';
			var colorList = ['#c23531', '#2f4554', '#61a0a8', '#d48265', '#91c7ae', '#749f83', '#ca8622',
				'#bda29a', '#6e7074',
				'#546570', '#c4ccd3'
			];
			var labelFont = 'bold 12px Sans-serif';
			var option = {
				backgroundColor: 'transparent',
				title: {
					show: false
				},
				legend: {
					show: false
				},
				visualMap: {
					show: false,
					seriesIndex: 4,
					dimension: 2,
					pieces: [{
						value: 1,
						color: downColor
					}, {
						value: -1,
						color: upColor
					}]
				},
				grid: [{
						top: '8%',
						left: 20,
						right: 20,
						height: '67%'
					},
					{
						top: '82%',
						left: 20,
						right: 20,
						height: '14%'
					},
				],
				axisPointer: { //坐标轴指示器配置项
					link: {
						xAxisIndex: 'all'
					},
					label: {
						backgroundColor: 'rgba(0, 0, 0, 0.9)',
						color: '#ffffff',
						borderColor: '#00d4ff',
						borderWidth: 1,
						borderRadius: 3,
						fontSize: 10,
						shadowBlur: 2,
						shadowColor: '#00d4ff'
					}
				},
				xAxis: [{
					type: 'category', //坐标轴类型。(value:数值轴，适用于连续数据。,category:类目轴，适用于离散的类目数据,time: 时间轴，适用于连续的时序数据,log:对数轴。适用于对数数据)
					data: [], //类目数据，在类目轴（type: 'category'）中有效。
					scale: true,
					boundaryGap: false, //坐标轴两边留白策略，类目轴和非类目轴的设置和表现不一样。
					axisLine: {
						show: false
					}, //坐标轴轴线相关设置
					axisTick: {
						show: false
					}, //坐标轴刻度相关设置。
					axisLabel: {
						show: false,
					}, //坐标轴刻度标签的相关设置。
					splitLine: {
						show: false,
						lineStyle: {
							color: 'rgba(255,255,255, 0.1)'
						}
					}, //坐标轴在 grid 区域中的分隔线。
					min: 'dataMin', //坐标轴刻度最小值。可以设置成特殊值 'dataMin'，此时取数据在该轴上的最小值作为最小刻度。
					max: 'dataMax', //坐标轴刻度最大值。可以设置成特殊值 'dataMax'，此时取数据在该轴上的最大值作为最大刻度。
					axisPointer: {
						label: {
							margin: 200
						}
					},
				}, {
					type: 'category',
					gridIndex: 1, //x 轴所在的 grid 的索引，默认位于第一个 grid。
					data: [], //类目数据，在类目轴（type: 'category'）中有效。
					scale: true,
					boundaryGap: false, //坐标轴两边留白策略，类目轴和非类目轴的设置和表现不一样。
					axisLine: {
						show: false,
						lineStyle: {
							color: 'rgba(255,255,255,1)',
							width: 1
						}
					}, //坐标轴轴线相关设置
					axisTick: {
						show: false
					}, //坐标轴刻度相关设置。
					axisLabel: { //坐标轴刻度标签的相关设置。
						show: true,
						margin: 6,
						fontSize: 10,
						color: 'rgba(99, 117, 139, 0)',
						formatter: function(value) {
							return echarts.format.formatTime('MM-dd', value);
						}
					},
					splitNumber: 20,
					splitLine: {
						show: false,
						lineStyle: {
							color: 'rgba(255,255,255, 0.1)'
						}
					}, //坐标轴在 grid 区域中的分隔线。
					min: 'dataMin', //坐标轴刻度最小值。可以设置成特殊值 'dataMin'，此时取数据在该轴上的最小值作为最小刻度。
					max: 'dataMax', //坐标轴刻度最大值。可以设置成特殊值 'dataMax'，此时取数据在该轴上的最大值作为最大刻度。
					// axisPointer: { show: true, type: 'none', label: { show: false }},
				}],
				yAxis: [{
					type: 'value', //坐标轴类型。(value:数值轴，适用于连续数据。,category:类目轴，适用于离散的类目数据,time: 时间轴，适用于连续的时序数据,log:对数轴。适用于对数数据)
					position: 'right', //y 轴的位置。'left','right'
					scale: true, //是否是脱离 0 值比例。设置成 true 后坐标刻度不会强制包含零刻度。在双数值轴的散点图中比较有用。(在设置 min 和 max 之后该配置项无效。)
					axisLine: {
						show: true
					}, //坐标轴轴线相关设置。
					axisTick: {
						show: true,
						inside: true
					}, //坐标轴刻度相关设置。
				axisLabel: { //坐标轴刻度标签的相关设置。
					show: true,
					color: 'rgb(97, 73, 183)',
					inside: true,
					fontSize: 11,
					fontWeight: 'bold',
					formatter: function(value) {
						return Number(value).toFixed(2)
					}
				},
					splitLine: {
						show: false,
						lineStyle: {
							color: 'rgba(255,255,255, 0.1)'
						}
					}, //坐标轴在 grid 区域中的分隔线。
				}, {
					type: 'value',
					position: 'right',
					scale: true,
					gridIndex: 1,
					axisLine: {
						show: false
					},
					axisTick: {
						show: false
					},
					axisLabel: {
						show: false
					},
					splitLine: {
						show: false
					}
				}],

				animation: true, //是否开启动画。
				color: colorList,
				tooltip: {
					show: true, //是否显示提示框组件，包括提示框浮层和 axisPointer。
					trigger: 'axis', //触发类型。item,axis,none
					formatter(params) {
						let tooltip = '';
						let time = '',
							open = 0,
							high = 0,
							low = 0,
							close = 0,
							amount = 0;
						for (var i = 0; i < params.length; i++) {
							if (params[i].seriesName === '日K') {
								time = params[i].name;
								open = params[i].data.length > 1 ? Number(that.formatterNum(params[i].data[
									1], 2)) : 0;
								close = params[i].data.length > 1 ? Number(that.formatterNum(params[i].data[
									2], 2)) : 0;
								low = params[i].data.length > 1 ? Number(that.formatterNum(params[i].data[
									3], 2)) : 0;
								high = params[i].data.length > 1 ? Number(that.formatterNum(params[i].data[
									4], 2)) : 0;
								amount = params[i].data.length > 1 ? Number(that.formatterNum(params[i]
									.data[5], 2)) : 0;

								// 判断涨跌颜色
								var priceColor = close >= open ? '#10b981' : '#ef4444';
								var changePercent = open > 0 ? (((close - open) / open) * 100).toFixed(2) : 0;
								var changeSymbol = changePercent >= 0 ? '+' : '';
								
								// 检测主题模式，决定最高价颜色
								var isLightTheme = document.body.classList.contains('theme-light');
								var highColor = isLightTheme ? '#ff0000' : '#10b981';
								
								tooltip = '<div class="charts-tooltip">' +
									'<div class="charts-tooltip-row"><div class="ctr-label">' + 'Time' +
									'</div><div class="ctr-value">' + time + '</div></div>' +
									'<div class="charts-tooltip-row"><div class="ctr-label">' + 'Open' +
									'</div><div class="ctr-value">' + open + '</div></div>' +
									'<div class="charts-tooltip-row"><div class="ctr-label">' + 'High' +
									'</div><div class="ctr-value" style="color: ' + highColor + ';">' + high + '</div></div>' +
									'<div class="charts-tooltip-row"><div class="ctr-label">' + 'Low' +
									'</div><div class="ctr-value" style="color: #ef4444;">' + low + '</div></div>' +
									'<div class="charts-tooltip-row"><div class="ctr-label">' + 'Close' +
									'</div><div class="ctr-value" style="color: ' + priceColor + '; font-weight: 700;">' + close + '</div></div>' +
									'<div class="charts-tooltip-row"><div class="ctr-label">' + 'Change' +
									'</div><div class="ctr-value" style="color: ' + priceColor + '; font-weight: 700;">' + changeSymbol + changePercent + '%</div></div>' +
									'<div class="charts-tooltip-row"><div class="ctr-label">' + 'Volume' +
									'</div><div class="ctr-value">' + amount + '</div></div></div>';
							}
							if (params[i].seriesName === 'MA5') {
								that.MA5 = params[i].data !== 'NAN' ? Number(that.formatterNum(params[i]
									.data, 2)) : 0
							}
							if (params[i].seriesName === 'MA10') {
								that.MA10 = params[i].data !== 'NAN' ? Number(that.formatterNum(params[i]
									.data, 2)) : 0
							}
							if (params[i].seriesName === 'MA30') {
								that.MA30 = params[i].data !== 'NAN' ? Number(that.formatterNum(params[i]
									.data, 2)) : 0
							}
							if (params[i].seriesName === 'VolumeMA5') {
								that.volMA5 = params[i].data !== 'NAN' ? Number(that.formatterNum(params[i]
									.data, 2)) : 0
							}
							if (params[i].seriesName === 'VolumeMA10') {
								that.volMA10 = params[i].data !== 'NAN' ? Number(that.formatterNum(params[i]
									.data, 2)) : 0
							}
						}
						return tooltip;
					},
					triggerOn: 'click', //提示框触发的条件 'mousemove','click','mousemove|click','none'
					textStyle: {
						fontSize: 10,
						fontWeight: '500'
					}, //提示框浮层的文本样式
					backgroundColor: 'linear-gradient(135deg, rgba(55, 65, 85, 0.95), rgba(75, 85, 105, 0.95))', //提示框浮层的背景颜色。
					borderColor: 'rgba(255, 255, 255, 0.3)', //提示框浮层的边框颜色。
					borderWidth: 1,
					borderRadius: 8,
					shadowBlur: 20,
					shadowColor: 'rgba(0, 0, 0, 0.3)',
					shadowOffsetX: 0,
					shadowOffsetY: 4,
					position: function(pos, params, el, elRect, size) { //提示框浮层的位置，默认不设置时位置会跟随鼠标的位置。
						var obj = {
							top: 20
						};
						obj[['left', 'right'][+(pos[0] < size.viewSize[0] / 2)]] = 10;
						return obj;
					},
					axisPointer: { //坐标轴指示器配置项。
						label: {
							color: '#ffffff',
							fontSize: 10,
							backgroundColor: 'rgba(0, 0, 0, 0.9)',
							borderColor: "#00d4ff",
							shadowBlur: 2,
							shadowColor: '#00d4ff',
							borderWidth: 1,
							padding: [4, 6, 4, 6],
						},
						animation: false,
						type: 'cross',
						lineStyle: {
							color: '#00d4ff',
							width: 1,
							type: 'solid',
							opacity: 0.8
						}
					}
				},

				dataZoom: [{ //用于区域缩放
					type: 'inside',
					xAxisIndex: [0, 1],
					realtime: false,
					start: 0,
					end: 100,
				}],
				series: [{
						type: 'candlestick',
						name: '日K',
						data: [],
						itemStyle: {
							color: upColor,
							color0: downColor,
							borderColor: upColor,
							borderColor0: downColor
						},
						markPoint: {
							symbol: 'rect',
							symbolSize: [-10, 0.5],
							symbolOffset: [5, 0],
							itemStyle: {
								color: 'rgba(255,255,255,.87)'
							},
							label: {
								color: 'rgba(255,255,255,.87)',
								offset: [10, 0],
								fontSize: 10,
								align: 'left',
								formatter: function(params) {
									return Number(params.value).toFixed(2)
								}
							},
							data: [{
									name: 'max',
									type: 'max',
									valueDim: 'highest'
								},
								{
									name: 'min',
									type: 'min',
									valueDim: 'lowest'
								}
							]
						},
						markLine: {
							symbol: ['none'],
							data: [
								{ 
									yAxis: '1939',
									name: "new_v"
								}
							],
							lineStyle: {
								color: '#fe5c57'
							},
							labelLine: {
								show: true,
								length: 10, // 箭头长度  
								length2: 15, // 箭头尾部长度  
								color: '#666', // 线条颜色  
								symbol: 'arrow', // 箭头符号  
								symbolSize: 10, // 箭头大小  
								position: 'start' // 箭头的位置，start表示在起点，end表示在终点  
							},
							label: {
								show: true,
								position: 'middle', // 标签位置，middle表示在箭线上方居中，start和end表示在箭头的起点或终点  
								color: '#fe5c57',
								fontSize: 12,
								backgroundColor: 'rgba(0, 0, 0, 0.6)',
								borderColor: "rgba(0, 0, 0, 0.6)",
								shadowBlur: 0,
								borderWidth: 0.5,
								padding: [4, 2, 3, 2],
								formatter: function(params) {
									var value = (params.value > 0) ? params.value : '';
									return value;
								}
							}
						}
					},
					{
						name: 'MA5',
						type: 'line',
						data: [],
						symbol: 'none', //去除圆点
						smooth: true,
						lineStyle: {
							normal: {
								opacity: 1,
								width: 2,
								color: "#ffff00"
							}
						},
						z: 5
					},
					{
						name: 'MA10',
						type: 'line',
						data: [],
						symbol: 'none', //去除圆点
						smooth: true,
						lineStyle: {
							normal: {
								opacity: 1,
								width: 2,
								color: '#00ff88'
							}
						},
						z: 4
					},
					{
						name: 'MA30',
						type: 'line',
						data: [],
						symbol: 'none', //去除圆点
						smooth: true,
						lineStyle: {
							normal: {
								opacity: 1,
								width: 2,
								color: '#ff6600'
							}
						},
						z: 3
					},
					{
						name: 'Volume',
						type: 'bar',
						xAxisIndex: 1,
						yAxisIndex: 1,
						data: []
					},
					{
						name: 'VolumeMA5',
						type: 'line',
						xAxisIndex: 1,
						yAxisIndex: 1,
						data: [],
						symbol: 'none', //去除圆点
						smooth: true,
						lineStyle: {
							normal: {
								opacity: 1,
								width: 2,
								color: "#ffff00"
							}
						},
						z: 5
					},
					{
						name: 'VolumeMA10',
						type: 'line',
						xAxisIndex: 1,
						yAxisIndex: 1,
						data: [],
						symbol: 'none', //去除圆点
						smooth: true,
						lineStyle: {
							normal: {
								opacity: 1,
								width: 2,
								color: '#00ff88'
							}
						},
						z: 4
					},
				]
			};
			myChart.setOption(option);
			// 加载上一页数据
			myChart.on('datazoom', function(params) {
				let num = params.batch[0]['start'];
				if (num == 0) {
					// 已到最左边
				}
			})
			// 优化resize事件监听器，使用防抖和被动模式
			let resizeTimer = null;
			const resizeHandler = () => {
				if (resizeTimer) {
					clearTimeout(resizeTimer);
				}
				resizeTimer = setTimeout(() => {
					if (myChart && !myChart.isDisposed()) {
						myChart.resize();
					}
				}, 100);
			};
			
			// 使用被动事件监听器
			try {
				window.addEventListener('resize', resizeHandler, { passive: true });
			} catch (e) {
				// 降级到传统模式
				window.addEventListener('resize', resizeHandler, false);
			}
		}
	}
})