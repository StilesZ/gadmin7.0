
/**
 * 独立弹窗层级处理
 */
function LayerPagesLevelHandle(key)
{

    var index = 0;
    var $content = $('.layui-body');
    $content.find('.window-layer-alone-layer').each(function()
    {
        var temp_index = parseInt($(this).css('z-index') || 0);
        if(temp_index > index)
        {
            index = temp_index
        }
        $(this).parent().show();
    });
    $content.find('.window-layer .window-layer-seat').show();
    var $layer = $content.find('#tabsbody-'+key);
    $layer.css({'z-index': index+1, 'position': 'fixed'});
    $layer.find('.window-layer-seat').hide();
}

$(function()
{
    // 双击为独立窗口
    $(document).on('dblclick', '.layui-tab-title li', function()
    {
        var key = $(this).data('key') || null;
        // 增加独立窗口类
        $('#tabsbody-'+key).addClass('window-layer-alone-layer').show();
        // 设置层级
        var index = 0;
        $('#tabsbody-'+key).parent().find('.window-layer-alone-layer').each(function()
        {
            var temp_index = parseInt($(this).css('z-index') || 0);
            if(temp_index > index)
            {
                index = temp_index
            }
        });
        $('#tabsbody-'+key).css({'margin': 0, 'position': 'fixed', 'z-index': index+1});
        // 设置层级
        LayerPagesLevelHandle(key);
    });
    // 双击为独立窗口
    var window_layer_alone_layer_warning_timer = null;
    $(document).on('click', '.layui-tab-title li', function()
    {
        // 选中当前页面
        $('.layui-tab-title li').removeClass('am-active');
        $(this).addClass('am-active');
        var key = $(this).data('key');
        var $content = $('.layui-body');
        // 显示当前页面
        $content.find('.window-layer').not('.window-layer-alone-layer').hide();
        var $current_iframe = $('.layui-body #tabsbody-'+key);
        $current_iframe.show();
        // 窗口存在独立
        if($current_iframe.hasClass('window-layer-alone-layer'))
        {
            // 窗口存在独立则警告窗口提示
            var count = 0;
            clearInterval(window_layer_alone_layer_warning_timer);
            window_layer_alone_layer_warning_timer = setInterval(function()
            {
                if(count > 10)
                {
                    clearInterval(window_layer_alone_layer_warning_timer);
                } else {
                    $current_iframe.css('box-shadow', 'rgb(0 0 0 / 30%) 1px 1px '+(count%2 == 0 ? '12px' : '24px'));
                    count++;
                }
            }, 50);
            // 设置层级
            LayerPagesLevelHandle(key);

        } else {
            // 非独立窗口则隐藏所有页面占位
            $content.find('.window-layer .window-layer-seat').hide();
        }
    });

    // 窗口切换
    $(document).on('click', '.layui-body .window-layer', function()
    {
        if($(this).hasClass('window-layer-alone-layer'))
        {
            LayerPagesLevelHandle($(this).data('key'));
        } else {
            // 非独立窗口则隐藏所有页面占位
            $('.layui-body .window-layer .window-layer-seat').hide();
        }
    });

    // 独立窗口刷新
    $(document).on('click', '.window-layer-alone-layer .window-layer-tab-bar .refresh', function()
    {
        var $parent = $(this).parents('.window-layer');
        if($parent.find('iframe').attr('src', $parent.find('iframe').attr('src')));
    });

    // 收回独立窗口
    $(document).on('click', '.window-layer-alone-layer .window-layer-tab-bar .recovery', function()
    {
        // 移除class和样式
        $(this).parents('.window-layer').removeClass('window-layer-alone-layer').css({'position':'', 'left':'', 'top':'', 'box-shadow':'', 'width':'', 'height':'', 'z-index':''});
        // 显示已选中页面
        var key = $('.layui-tab-title li.am-active').data('key') || null;
        if(key != null)
        {
            $('#ifcontent .window-layer').not('.window-layer-alone-layer').hide();
            $('#ifcontent .iframe-item-key-'+key).show();
        }
        // 阻止事件
        return false;
    });

    // 移除独立窗口
    $(document).on('click', '.window-layer-alone-layer .window-layer-tab-bar .close', function()
    {
        // 移除当前页面
        var $parent = $(this).parents('.window-layer');
        var key = $parent.data('key');
        $parent.remove();
        $('#tabsli-'+key).remove();
        $('#tabsitem-'+key).remove();

        // 当前没有选中的导航则模拟点击最后一个选中
        if($('.layui-tab-title li.am-active').length == 0)
        {
            $('.layui-tab-title li:last').trigger('click');
        }
        // 无页面则添加默认初始化页面
        if($('.layui-tab-title li').length == 0)
        {
            $('#ifcontent .window-layer').show();
        }
        return false;
    });

    // 弹窗拖拽
    $(document).on('mousedown', '.window-layer-alone-layer .window-layer-tab-bar', function(pe)
    {
        var is_move = true;
        var $content = $('.layui-body');
        var $layer = $(this).parents('.window-layer');
        var $layer_seat = $layer.find('.window-layer-seat');
        var header_height = $('header.admin-header').height();
        var menu_width =250;
        var width = $layer.outerWidth();
        var height = $layer.outerHeight();
        var win_width = $content.width()+menu_width;
        var win_height = $content.height()+header_height;
        var abs_x = pe.pageX - $layer.offset().left;
        var abs_y = pe.pageY - $layer.offset().top;
        // 设置层级
        LayerPagesLevelHandle($layer.data('key'));
        $layer_seat.show();
        $(document).mousemove(function(event)
        {
            if(is_move)
            {
                // 左
                var left = event.pageX - abs_x
                if(left < menu_width)
                {
                    left = menu_width
                } else if (left > win_width - width)
                {
                    left = win_width - width;
                }

                // 上
                var top = event.pageY - abs_y;
                if(top < header_height)
                {
                    top = header_height;
                }
                if (top > win_height - height)
                {
                    top = win_height - height
                }

                // 设置层级
                var index = 0;
                $layer.parent().find('.window-layer-alone-layer').each(function()
                {
                    var temp_index = parseInt($(this).css('z-index') || 0);
                    if(temp_index > index)
                    {
                        index = temp_index
                    }
                });
                $layer.css({'left':left, 'top':top, 'margin': 0, 'position': 'fixed', 'z-index': index+1});
            };
        }).mouseup(function()
        {
            if(is_move)
            {
                $layer_seat.hide();
            }
            is_move = false;
        }).mouseleave(function()
        {
            if(is_move)
            {
                $layer_seat.hide();
            }
            is_move = false;
        });
    });

    // 独立窗口拉动大小
    $(document).on('mousedown', '.window-layer-alone-layer .window-layer-resize-bar div[class^="window-layer-resize-item-"]', function(pe)
    {
        var is_move = true;
        var $content = $('.layui-body');
        var $layer = $(this).parents('.window-layer');
        var $layer_seat = $layer.find('.window-layer-seat');
        var py = pe.pageY;
        var px = pe.pageX;
        var resize_bar_type = $(this).data('type');
        var p_init_y = $layer.css('top').replace('px', '');
        var p_init_x = $layer.css('left').replace('px', '');
        var p_init_height = $layer.height();
        var p_init_width = $layer.width();
        var header_height = $('header.admin-header').height();
        var menu_width = 250;
        var win_width = $content.width()+menu_width;
        var win_height = $content.height();
        var limit_min_width = 500;
        var limit_min_height = 300;
        // 设置层级
        LayerPagesLevelHandle($layer.data('key'));
        $layer_seat.show();
        $(document).mousemove(function(event)
        {
            if(is_move)
            {
                var hh = parseInt(event.pageY) - parseInt(py);
                var ww = parseInt(event.pageX) - parseInt(px);
                var temp_y = hh + parseInt(p_init_y);
                var temp_x = ww + parseInt(p_init_x);
                if(temp_y < header_height)
                {
                    temp_y = header_height;
                }

                // 高度
                var height = 0;
                if(['left-top', 'top', 'right-top'].indexOf(resize_bar_type) != -1)
                {
                    height = parseInt(p_init_height) - hh;
                }
                if(['left-bottom', 'bottom', 'right-bottom'].indexOf(resize_bar_type) != -1)
                {
                    height = parseInt(p_init_height) + hh;
                }
                if(height > win_height)
                {
                    height = win_height;
                }
                if(height < limit_min_height)
                {
                    height = limit_min_height;
                }

                // 宽度
                var width = 0;
                if(['left-top', 'left', 'left-bottom'].indexOf(resize_bar_type) != -1)
                {
                    width = parseInt(p_init_width) - ww;
                }
                if(['right-top', 'right', 'right-bottom'].indexOf(resize_bar_type) != -1)
                {
                    width = parseInt(p_init_width) + ww;
                }
                if(width > win_width)
                {
                    width = win_width;
                }
                if(width < limit_min_width)
                {
                    width = limit_min_width;
                }

                // 不允许超出外边距范围
                if(event.pageY-header_height <= 0 || event.pageY >= win_height+header_height)
                {
                    return false;
                }
                if(event.pageX >= win_width)
                {
                    return false;
                }
                if(event.pageX <= menu_width)
                {
                    return false;
                }

                // 根据类型设置样式
                switch(resize_bar_type)
                {
                    case 'left-top':
                        $layer.css({
                            top: temp_y + 'px',
                            height: height + 'px',
                            left: temp_x + 'px',
                            width: width + 'px'
                        });
                        break;
                    case 'top':
                        $layer.css({
                            top: temp_y + 'px',
                            height: height + 'px'
                        });
                        break;
                    case 'right-top':
                        $layer.css({
                            top: temp_y + 'px',
                            height: height + 'px',
                            width: width + 'px'
                        });
                        break;
                    case 'left':
                        $layer.css({
                            left: temp_x + 'px',
                            width: width + 'px'
                        });
                        break;
                    case 'right':
                        $layer.css({
                            width: width + 'px'
                        });
                        break;
                    case 'left-bottom':
                        $layer.css({
                            height: height + 'px',
                            left: temp_x + 'px',
                            width: width + 'px'
                        });
                        break;
                    case 'bottom':
                        $layer.css({
                            height: height + 'px'
                        });
                        break;
                    case 'right-bottom':
                        $layer.css({
                            height: height + 'px',
                            width: width + 'px'
                        });
                        break;
                }
            }
        }).mouseup(function()
        {
            if(is_move)
            {
                $layer_seat.hide();
            }
            is_move = false;
        }).mouseleave(function()
        {
            if(is_move)
            {
                $layer_seat.hide();
            }
            is_move = false;
        });
    });
});