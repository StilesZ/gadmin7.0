/*!
 * desk common
 * http://cojz8.com
 *
 * 
 * Auth:Guoguo(632522043@qq.com)
 * http://cojz8.com
 *
 * Date: 2021年1月22日20:36:23
 */
var desk = {
	pie : function(Id,Data) {
		var echarts4 = echarts.init(document.getElementById(Id));
		var echarts4_option = {
		  label: {
			show: true,
			formatter: function(param) {
			  return param.name + '\n' + param.value;
			}
		  },
		  color: ['#4da7fd', '#888888', '#df5667', '#1890ff', '#eeeeee'],
		  series : [
			{
			  type: 'pie',
			  radius : '65%',
			  selectedMode: 'single',
			  data:Data
			}
		  ]
		}
		echarts4.setOption(echarts4_option);
	},
	bar : function(Id,yAxis,data) {
		var echarts3 = echarts.init(document.getElementById(Id));
		 var echarts1_option = {
			  title: {
				show: false
			  },
			  color: ['#1890ff'],
			  tooltip: {
				trigger: 'item',
			  },
			  grid: {
						top: '3%',
				left: '3%',
				right: '4%',
				bottom: '3%',
				containLabel: true
			  },
			  xAxis: {
				type: 'value',
				boundaryGap: [0, 0.01]
			  },
			  yAxis: {
				type: 'category',
				data: yAxis
			  },
			  barWidth : 24,
			  emphasis: {
				itemStyle: {
				  shadowColor: 'rgba(0, 0, 0, 0.5)',
				  shadowBlur: 2,
				}
			  },
			  series: [
				{
				  type: 'bar',
				  data : data,
				}
			  ]
			}
			echarts3.setOption(echarts1_option);
	},
	line : function(Id,xData,yData2) {
		var echarts3 = echarts.init(document.getElementById(Id));
		var echarts1_option = {
			  tooltip: {
				trigger: 'axis'
			  },
			  legend: {
				x : 'center',
				y : 'top',
				data: ['会员','非会员']
			  },
			  xAxis: {
				data: xData,
			  },
			  yAxis:{
				type: 'value'
			  },
			  series: yData2
			}
			echarts3.setOption(echarts1_option);
	},
	lclose : function(title,url,w,h) {
		var index = parent.layer.getFrameIndex(window.name);
		parent.layer.close(index);
	}
	
}