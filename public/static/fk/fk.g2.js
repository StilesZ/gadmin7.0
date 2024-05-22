/*
 * desk common
 */
var color = ['#4C2D9C', '#CC295A', '#275D94', '#F1A19B', '#7E65C1', '#C059B7', '#123F6E', '#EED653', '#E47F2E', '#2F1574', '#FFF1CB'];
var desk = {
    int : function(Type,Id) {
        var ChartData = (JSON.parse(localStorage.getItem("fk_data"))).chart[Id];
        if(ChartData.type==1){
            var Data = ChartData.fun;//从缓存数据获取
        }
        if(ChartData.type==2){
            Fkreport.sFun(sfun_url,{'fun':ChartData.fun,'Type':Type,'Id':Id},desk.funSet);
            return;
        }
        if(Data=='' || Data== undefined){
            Data = '[]';
        }
        desk.funSet({Id:Id,Type:Type,Data:Data});/*渲染图表*/
    },
    funSet : function(res) {
        var FunData = res.Data;
        if($.isNumeric( FunData )){
            var data = FunData;
        }else{
            if(res.Type=='pie'||res.Type=='bar'||res.Type=='area'||res.Type=='line'||res.Type=='rosePlot'){
                 var data = eval('(' + FunData + ')');
            }else{
                var data =FunData.value;
            }
        }

        if(res.Type=='pie'){
            desk.pie(res.Id,data);
        }
        if(res.Type=='bar'){
            desk.bar(res.Id,data);
        }
        if(res.Type=='line'){
            desk.line(res.Id,data);
        }
        if(res.Type=='area'){
            desk.area(res.Id,data);
        }
        if(res.Type=='rosePlot'){
            desk.rosePlot(res.Id,data);
        }
        if(res.Type=='gauge'){
            desk.gauge(res.Id,data);
        }
        if(res.Type=='Liquid'){
            desk.Liquid(res.Id,data);
        }
    },
    pie : function(Id,data) {
        const { Pie } = G2Plot;
        const piePlot = new Pie(Id, {
            data,
            angleField: 'value',
            colorField: 'type',
            radius: 1,
            innerRadius: 0.6,
            label: {
                type: 'inner',
                offset: '-50%',
                content: '{value}',
                style: {
                    textAlign: 'center',
                    fontSize: 14,
                },
            },
            interactions: [{ type: 'element-active' }],
            statistic: {
                title: false,
                content: {
                    style: {
                        whiteSpace: 'pre-wrap',
                        overflow: 'hidden',
                        textOverflow: 'ellipsis',
                    },
                    content: '',
                },
            }
        });
        piePlot.render();
    },
    bar : function(Id,data) {
        const { Column } = G2Plot;

        if(data[0].item == undefined){
            var isGroup = false;
        }else{
            var seriesField = 'item';
            var isGroup = true;
        }

        const stackedColumnPlot = new Column(Id,{
            data,
            isStack: true,
            xField: 'type',
            yField: 'value',
            seriesField: seriesField || '',
            isGroup: isGroup,
            label: {
                position: 'top',
                layout: [{ type: 'interval-adjust-position' }, { type: 'interval-hide-overlap' }, { type: 'adjust-color' }],
            },

        });
        stackedColumnPlot.render();
    },
    line : function(Id,data) {
        const { Line } = G2Plot;
        if(data[0].item == undefined){
            var isGroup = false;
        }else{
            var seriesField = 'item';
            var isGroup = true;
        }
        const line = new Line(Id, {
            data,
            xField: 'type',
            yField: 'value',
            seriesField: seriesField || '',
            animation: {
                appear: {
                    animation: 'path-in',
                    duration: 5000,
                },
            },
        });
        line.render();
    },
    area : function(Id,data) {
        const { Area } = G2Plot;
        const area = new Area(Id, {
            data,
            padding: 'auto',
            xField: 'type',
            yField: 'value',
            xAxis: {
                range: [0, 1],
                tickCount: 5,
            },
            areaStyle: () => {
                return {
                    fill: 'l(270) 0:#ffffff 0.5:#7ec2f3 1:#1890ff',
                };
            },
        });
        area.render();
    },
    rosePlot : function(Id,data) {
        const { Rose } = G2Plot;
        const rosePlot = new Rose(Id, {
            data,
            xField: 'type',
            yField: 'value',
            seriesField: 'type',
            radius: 0.9,
            legend: {
                position: 'bottom',
            },
            interactions: [{ type: 'element-active' }],
        });
        rosePlot.render();
    },
    gauge : function(Id,FunData) {
        const { Gauge } = G2Plot;
        const gauge = new Gauge(Id, {
            percent: Number(FunData),
            range: {
                ticks: [0, 1/3, 2/3, 1],
                color: ['#F4664A', '#FAAD14', '#30BF78'],
            },
            indicator: {
                pointer: {
                    style: {
                        stroke: '#D0D0D0',
                    },
                },
                pin: {
                    style: {
                        stroke: '#D0D0D0',
                    },
                },
            },
            statistic: {
                content: {
                    style: {
                        fontSize: '36px',
                        lineHeight: '36px',
                    },
                },
            },
        });
        gauge.render();
    },
    Liquid : function(Id,FunData) {
        const { Liquid } = G2Plot;
        const liquidPlot = new Liquid(Id, {
            percent: FunData,
            wave: {
                length: 128,
            },
        });
        liquidPlot.render();
    }
}
