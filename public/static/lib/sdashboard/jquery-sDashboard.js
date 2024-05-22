/*
 * jquery sDashboard (2.5)
 * Copyright 2012, Model N, Inc
 * Distributed under MIT license.
 * https://github.com/ModelN/sDashboard
 */

( function(factory) {"use strict";
		if ( typeof define === 'function' && define.amd) {
			define(['jquery', 'Flotr'], factory);
		} else {
			factory($, Flotr);
		}
	}(function($, Flotr) {"use strict";
		$.widget("mn.sDashboard", {
			version : "2.5",
			options : {
				dashboardData : []
			},
			_create : function() {
				this.element.addClass("sDashboard");
				this._createView();
			},
			_createView : function() {
				var docHeight = $(document).height();
				$("body").append("<div class='sDashboard-overlay'></div>");
				$(".sDashboard-overlay").height(docHeight);
				$(".sDashboard-overlay").hide();
				var _dashboardData = this.options.dashboardData;
				var i;
				for ( i = 0; i < _dashboardData.length; i++) {
					var widget = this._constructWidget(_dashboardData[i]);
					this.element.append(widget);
				}
			},
			_constructWidget : function(widgetDefinition) {
				var widget = $("<li/>").attr({"id":widgetDefinition.widgetId,"class":widgetDefinition.widgetWidth});
				var widgetContainer = $("<div/>").addClass("sDashboardWidget");
				if(widgetDefinition.showtitle == 0){
					var widgetHeader = $("<div/>").addClass("sDashboardWidgetHeader sDashboard-clearfix").append(widgetDefinition.widgetTitle);
					var widgetContent = $("<div/>").addClass("sDashboardWidgetContent ").css('height',(widgetDefinition.widgetHeight || '180px'));
				}else{
					var widgetContent = $("<div/>").addClass("sDashboardWidgetContent notitle").css('height',(widgetDefinition.widgetHeight || '180px'));
				}
				widgetContent.append(widgetDefinition.widgetContent);
				widgetContainer.append(widgetHeader);
				widgetContainer.append(widgetContent);
				widget.append(widgetContainer);
				return widget;
			},
		});
	}));

