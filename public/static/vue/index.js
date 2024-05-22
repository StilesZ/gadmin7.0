/*!
 * Gadmin 企业级开发平台
 *
 * Date: 2021年2月1日23:34:39
 */
const themeData = {
    defaultTheme: {
        logo: '#20222A',
        selected: '#3963bc',
        header: '#fff',
        sideBar: '#20222A',
        navBar: '#fff',
        alias: '默认主题'
    },
    pink: {
        logo: '#50314F',
        selected: '#7A4D7B',
        header: '#fff',
        sideBar: '#50314F',
        navBar: '#7A4D7B',
        alias: '紫色迷情'
    },
    black: {
        logo: '#20222A',
        selected: '#3e3e3e',
        header: '#fff',
        sideBar: '#20222A',
        navBar: '#3e3e3e',
        alias: '典雅黑'
    },
    green: {
        logo: '#226A62',
        selected: '#009688',
        header: '#fff',
        sideBar: '#20222A',
        navBar: '#009688',
        alias: '墨绿'
    },
    blue: {
        logo: 'rgb(24, 144, 255)',
        selected: 'rgb(57, 158, 253)',
        header: '#fff',
        sideBar: 'linear-gradient(90deg, rgb(0, 108, 255), rgb(57, 158, 253))',
        navBar: 'rgb(57, 158, 253)',
        alias: '海之蓝'
    },
    pro: {
        logo: 'rgb(32, 34, 42)',
        selected: 'rgb(230, 0, 18)',
        header: '#fff',
        sideBar: 'rgb(32, 34, 42)',
        navBar: 'rgb(255,0,24)',
        alias: '红黑经典'
    },
    red: {
        logo: 'rgb(230, 0, 18)',
        selected: 'rgb(32, 34, 42)',
        header: '#fff',
        sideBar: 'rgb(230, 0, 18)',
        navBar: 'rgb(255,0,24)',
        alias: '红色主题'
    },
    classical: {
        logo: '#2E241B',
        sideBar: '#2E241B',
        selected: '#A48566',
        header: '#fff',
        navBar: '#A48566',
        alias: '古典'
    }
}
function chooseTheme(color) {
    try {
        if (!color) color = 'defaultTheme';
        let styleScope = document.getElementById("theme-data")
        let layoutType = localStorage.getItem('classicalLayout') || 1
        styleScope.innerHTML = `
                                .layui-nav-tree .layui-this,
                                .layui-nav-tree .layui-this>a,
                                .layui-nav-tree .layui-nav-child dd.layui-this,
                                .layui-nav-tree .layui-nav-child dd.layui-this a{
                                    background-color:${themeData[color].selected} !important;
                                    ${parseInt(layoutType) === 0 ? 'color: #fff !important': ''}
                                }
                                .layui-header .layui-nav .layui-nav-more{
                                    border-top-color: ${(color !== 'defaultTheme' && themeData[color].header !== 'white' && themeData[color].header !== '#fff') ? '#fff' : '#333'} !important
                                }
                                .layui-header .layui-nav .layui-nav-mored{
                                    border-color: transparent transparent ${(color !== 'defaultTheme' && themeData[color].header !== 'white' && themeData[color].header !== '#fff') ? '#fff' : '#333'} !important
                                }
                                .g-admin-layout .layui-header .layui-nav .layui-this:after, .g-admin-layout .layui-header .layui-nav-bar,
                                .g-admin-layout .layui-header .layui-nav-bar {
                                    height: 2px;
                                    background-color: ${color !== 'defaultTheme' ? themeData[color].navBar : '#20222A'}
                                }
                                .g-admin-layout .layui-header a,
                                .g-admin-layout .layui-header a:hover,
                                .g-admin-layout .layui-header a cite{
                                    color: ${(color !== 'defaultTheme' && themeData[color].header !== 'white' && themeData[color].header !== '#fff') ? '#fff' : '#333'}
                                }
                                .drop-down a{
                                    color: #2d2d2d
                                }
                                .layui-nav-tree .layui-nav-bar{
                                    background-color: ${color !== 'defaultTheme' ? themeData[color].navBar : '#009688'}
                                }
                                .sideMenuBar{
                                    background: ${themeData[color].sideBar} !important
                                }
                                `
        if (parseInt(layoutType) === 0) {
            styleScope.innerHTML += `
                                        .layui-side-menu .layui-nav .layui-nav-item a:hover{
                                            background-color: ${themeData[color].selected} !important;
                                            color:#f2f2f2 !important;
                                        }
                                        .layui-side-menu .layui-nav .layui-nav-item a:hover span{
                                            border-top-color: rgba(255,255,255,0.7) !important
                                        }

                                        .selected-top-menu{
                                            background-color: ${themeData[color].selected} !important;
                                            color: #f2f2f2;
                                            border-top-right-radius: 7px;
                                            border-top-left-radius: 7px;
                                            border-bottom-right-radius: 7px;
                                            border-bottom-left-radius: 7px;
                                        }
                                        .layui-logo{
                                            background-color: #fff !important
                                        }
                                        .layui-nav-more{
                                            border-top-color: #5f626e44 !important;
                                        }
                                    `
        } else {
            styleScope.innerHTML += `
                                        .layui-logo{
                                            background-color: ${themeData[color].logo} !important
                                            
                                        }
                                        .layui-nav-more{
                                            border-top-color: #ffffff99;
                                        }
                                    `
        }

        document.getElementsByClassName("layui-side-menu")[0].style.background = themeData[color].sideBar
        document.getElementsByClassName("layui-header")[0].style.backgroundColor = themeData[color].header

        localStorage.setItem("themeColor", color)
    } catch (e) {
        console.log("设置主题失败")
    }

}
//JavaScript代码区域
const vm = new Vue({
    el: '#g-app-main',
    data() {
        return {
            tabsList: [], // 打开的tab页面
            showPagesAction: false, // 打开tabs操作选项
            selectedTabsIndex: -1, // 当前所选的tab 索引
            sideBarOpen: true,
            sideBarOpenData: 1,
            inFullScreenMode: false, // 是否处于全屏模式
            showThemePicker: false, // 显示主题选择器
            classicalLayout: true,
            menuTreeData: [], // 目录数据
            subMenuData: [], // 二级目录数据
            selectedTopMenuIndex: 0,
            subMenuTitle: '', // 二级目录标题
            selectedId: -1, // 当前激活的id
        }
    },
    methods: {
        selectTopMenu(index) {
            this.sideBarOpenData = 0;
            this.resizeSideBar(index);
            if (this.classicalLayout) return
            this.selectedTopMenuIndex = index
            this.selectedSubIndex = -1
            this.subMenuData = this.menuTreeData[index]
            /*判断是否需要加载page页面*/
            if(this.subMenuData.is_page==0){
                this.openTab(this.subMenuData.title,this.subMenuData.data,this.subMenuData.opentype)
            }
            console.log(this.subMenuData);
            this.subMenuTitle = this.menuTreeData[index].title
            this.sideBarOpen = true

        },
        changeFullScreenStatus() {
            if (this.inFullScreenMode) {
                this.exitFullScreen()
                return
            }
            this.fullScreen()
        },
        // 全屏事件
        fullScreen() {
            var element = window.document
                .documentElement; //若要全屏页面中div，var element= document.getElementById("divID");
            //IE 10及以下ActiveXObject  
            if (window.ActiveXObject) {
                var WsShell = new ActiveXObject('WScript.Shell')
                WsShell.SendKeys('{F11}');
            }
            //HTML W3C 提议  
            else if (element.requestFullScreen) {
                element.requestFullScreen();
            }
            //IE11  
            else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
            // Webkit (works in Safari5.1 and Chrome 15)  
            else if (element.webkitRequestFullScreen) {
                element.webkitRequestFullScreen();
            }
            // Firefox (works in nightly)  
            else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            }

            this.inFullScreenMode = true
        },
        exitFullScreen() {
            var element = document
                .documentElement; //若要全屏页面中div，var element= document.getElementById("divID");   
            //IE ActiveXObject  
            if (window.ActiveXObject) {
                var WsShell = new ActiveXObject('WScript.Shell')
                WsShell.SendKeys('{F11}');
            }
            //HTML5 W3C 提议  
            else if (element.requestFullScreen) {
                document.exitFullscreen();
            }
            //IE 11  
            else if (element.msRequestFullscreen) {
                document.msExitFullscreen();
            }
            // Webkit (works in Safari5.1 and Chrome 15)  
            else if (element.webkitRequestFullScreen) {
                document.webkitCancelFullScreen();
            }
            // Firefox (works in nightly)  
            else if (element.mozRequestFullScreen) {
                document.mozCancelFullScreen();
            }
            this.inFullScreenMode = false
        },
        // 改变侧边栏大小
        resizeSideBar(index) {
            if(index==0){
                return;
            }
            var elements = document.getElementsByClassName("g-admin-pagetabs");
            if(this.sideBarOpen && this.sideBarOpenData==1){
                for (var i = 0; i < elements.length; i++) {
                    elements[i].style.left = "150px";
                    elements[i].style.width = "calc(100% - 410px)";
                }
                this.sideBarOpenData = 0;
            }else{
                for (var i = 0; i < elements.length; i++) {
                    elements[i].style.left = "370px";
                    elements[i].style.width = "calc(100% - 610px)";
                }
                this.sideBarOpenData = 1;
            }
            this.sideBarOpen = !this.sideBarOpen
        },
        // 搜索模块
        searchModules() {

        },
        // 上一个页面
        async toLeftPage() {
            await this.$nextTick()
            const {offsetWidth,scrollWidth,offsetLeft} = this.$refs['iframe-tabs']
            if(scrollWidth <= offsetWidth){
                return
            }
            if(offsetLeft < 0){
                let leftDistance = offsetLeft + offsetWidth
                if(leftDistance > 0){
                    leftDistance = 0
                }
                this.$refs['iframe-tabs'].style.left = leftDistance + 'px'
            }
                        
        },
        // 下一个页面
        async toRightPage() {
            await this.$nextTick()
            const {offsetWidth,scrollWidth,offsetLeft} = this.$refs['iframe-tabs']
            if(scrollWidth <= offsetWidth){
                return
            }
            if(Math.abs(offsetLeft) < scrollWidth - offsetWidth){
                this.$refs['iframe-tabs'].style.left = offsetLeft-offsetWidth + 'px'
            }
        },
        // 切换到所选页面
        toSelectPage() {

        },
        // 重载当前页面
        reLoadCurrentPage() {
            this.$nextTick(() => {
                if (this.selectedTabsIndex === -1) {
                    this.$refs[`iframe-home`].contentWindow.location.reload();
                    return
                }
                this.$refs[`iframe-${this.selectedTabsIndex}`][0].contentWindow.location
                    .reload()
            })
        },

        // 关闭当前标签页
        closeCurrentTabs(index) {
            this.tabsList.splice(index, 1)
            $('#tabsbody-'+index).show()
            setTimeout(() => {
                if (this.tabsList.length > index) {
                    this.selectedTabsIndex = index
                } else {
                    this.selectedTabsIndex = index - 1
                }
            }, 300)
        },
        // 关闭所有
        closeAllTabs() {
            this.tabsList = []
            this.selectedTabsIndex = -1
        },
        // 关闭其他
        closeOtherTabs() {
            let tab = this.tabsList[this.selectedTabsIndex]
            this.tabsList = [tab]
            this.selectedTabsIndex = 0
        },
        openTab(name, src,type) {
            let newStr=src.indexOf("http");
            if(newStr==0){
                var realUrl = src
            }else{
                var location = src.split('?')
                if(location[0].indexOf("gadmin") != -1){
                    var realUrl = `${location[0]}`
                }else{
                    var realUrl = `/gadmin/${location[0]}.html`
                }
                if (location.length > 1) {
                    realUrl = realUrl + '?' + location[1]
                }
            }
            if(type==1){
                window.open(realUrl);
                return;
            }
            if(type==2){
                layer_show(name,realUrl,100,100);
                return;
            }
            if (this.tabsList.length >= 30) {
                layer.alert('最多可以打开30个标签页~');
                return;
            }
            let obj = {
                name,
                src: realUrl
            }
            const idx = this.checkExist(obj)
            if (idx > -1) {
                $('#tabsbody-'+(idx+1)).show()
                this.selectedTabsIndex = idx
                this.reLoadCurrentPage();
                return
            }
            this.tabsList.push(obj)
            this.selectedTabsIndex = this.tabsList.length - 1
        },
        checkExist(obj) {
            for (let i = 0; i < this.tabsList.length; i++) {
                const item = this.tabsList[i]
                if (item.name === obj.name && item.src === obj.src) {
                    return i
                }
            }
            return -1
        },
        generatePickerBody() {
            let tag = `
                    <div style="padding-left:10px;padding-right:10px;padding-top:10px">
                        <form class="layui-form" action="">
                            <div class="layui-form-item">
                                <select name="city" lay-verify="required">
                                    ${
                                        this.classicalLayout ? 
                                        "<option value='1' >经典</option><option value='0' >分栏</option>":
                                        "<option value='0' >分栏</option><option value='1' >经典</option>"
                                    }                       
                                </select>
                            </div>
                        </form>
                    </div>

                    <hr class="layui-border-black">
                    <div class='theme-picker'>
                    `
            for (const key in themeData) {
                if (themeData.hasOwnProperty(key)) {
                    let item = themeData[key]
                    tag += `
                    <div class="theme" style="background-color: ${item.logo}" onclick="chooseTheme('${key}')">
                        ${item.alias}
                    </div>
                    `
                }
            }
            tag += '</div>'
            return tag
        },
        openThemePicker() {
            const _this = this
            // const layer = layui.layer
            layer.open({
                type: 1,
                title: '颜色与布局',
                content: this.generatePickerBody(),
                area: ['300px', '800px'],
                offset: 'rb',
                shadeClose: true
            })
            const form = layui.form
            form.render()
            form.on("select", function (e) {
                _this.updateLayout(e.value)
            })
        },
        updateLayout(val) {
            if (!val) {
                val = 0
            }
            let layout = document.getElementById("layout-data")
            if (parseInt(val) === 0) {
                try {
                    this.classicalLayout = false
                    this.selectTopMenu(0)
                    layout.innerHTML = `
                        .g-admin-layout .layui-side{
                            width: 255px;
                            box-shadow: 0 2px 8px 0 #0000001a
                        }
                        .g-admin-layout .layui-logo, .layui-side-menu .layui-nav{
                            background-color: white;
                            position: absolute;
                            left: 65px;
                            height: 50px;
                            line-height:50px;
                            color: #333;
                            box-shadow: none;
                        }
                        .layui-side-menu .layui-side-scroll{
                            background-color: white;
                            width: 255px
                        }
                        .g-admin-layout .layui-logo,.sub-menu-divider,.layui-side-menu .layui-nav {
                            width: 185px;
                         }
                         .sub-menu-divider .line{
                            width: 50px;
                            }
                        .g-admin-pagetabs, .g-admin-layout .layui-body, .g-admin-layout .layui-footer, .g-admin-layout .g-layout-left{
                            left:255px;
                        }
                        .layui-side-menu .layui-nav .layui-nav-item a{
                            height: 30px;
                            line-height: 30px;
                            color:#5f626e;
                            display: flex;
                            align-items: center;
                        }
                        .layui-side-menu .layui-nav .layui-nav-item .layui-icon{
                            margin-top: -14px;
                        }          
                    `
                    localStorage.setItem("classicalLayout", "0")

                } catch (error) {
                    console.log(error);
                    layer.msg('加载失败')
                }
            } else {
                this.classicalLayout = true
                layout.innerHTML = ``
                localStorage.setItem("classicalLayout", "1")
            }
            let color = localStorage.getItem("themeColor")
            chooseTheme(color)
        }
    },
    async mounted() {
        try {
            const {
                data
            } = (await axios.get("/gadmin/index/getMenu")).data
            this.menuTreeData = data
            document.getElementById("main").style.display = "block"
            layer.close(loading)
            let color = localStorage.getItem("themeColor")
            chooseTheme(color)
            let val = localStorage.getItem('classicalLayout')
            this.updateLayout(val)
        } catch (error) {
            layer.msg("初始化失败,请重试")
        }
    }
})

window.vm = vm