/*
 *  jQuery table2excel - v1.1.1
 *  jQuery plugin to export an .xls file in browser from an HTML table
 *  https://github.com/rainabba/jquery-table2excel
 *
 *  Made by rainabba
 *  Under MIT License
 */
!function (a, b, c, d) {

    function e(b, c) {

        this.element = b, this.settings = a.extend({}, k, c), this._defaults = k, this._name = j, this.init()
    }

    function f(a) {

        return a.filename ? a.filename : "table2excel"
    }

    function g(a) {

        var b = /(\s+alt\s*=\s*"([^"]*)"|\s+alt\s*=\s*'([^']*)')/i;
        return a.replace(/<img[^>]*>/gi, function (a) {
            var c = b.exec(a);
            return null !== c && c.length >= 2 ? c[2] : ""
        })
    }

    function h(a) {
        return a.replace(/<a[^>]*>|<\/a>/gi, "")
    }

    function i(a) {
        var b = /(\s+value\s*=\s*"([^"]*)"|\s+value\s*=\s*'([^']*)')/i;
        return a.replace(/<input[^>]*>|<\/input>/gi, function (a) {
            var c = b.exec(a);
            return null !== c && c.length >= 2 ? c[2] : ""
        })
    }

    var j = "table2excel",
        k = {
            exclude: ".noExl",
            name: "Table2Excel",
            filename: "table2excel",
            fileext: ".xls",
            exclude_img: !0,
            exclude_links: !0,
            exclude_inputs: !0,
            columns: ""
        };
    e.prototype =
        {
            init: function () {
                var b = this;
                b.template = {
                    head: '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head>\x3c!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>',
                    sheet: {
                        head: "<x:ExcelWorksheet><x:Name>",
                        tail: "</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>"
                    }, mid: "</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--\x3e</head><body>",
                    table: {head: "<table>", tail: "</table>"}, foot: "</body></html>"
                },

                    b.tableRows = [],
                    a(b.element).each(function (c, d) {
                        var obj = (b.settings.columns + "").split(',');
                        var e = "";
                        a(d).find("tr").not(b.settings.exclude).each(
                            function (c, d) {
                                var count = 0;
                                e += "<tr>",
                                    a(d).find("td,th").not(b.settings.exclude).each(
                                        function (c, d) {
                                            var ishave = false;
                                            for (var i = 0; i < obj.length; i++) {
                                                if (obj[i] === (count + "")) {
                                                    ishave = true;
                                                }
                                            }
                                            if (!ishave) {
                                                var f = {
                                                    rows: a(this).attr("rowspan"),
                                                    cols: a(this).attr("colspan"),
                                                    flag: a(d).find(b.settings.exclude)
                                                };
                                                f.flag.length > 0 ? e += "<td> </td>" : f.rows & f.cols ? e += "<td>" + a(d).html() + "</td>" : (e += "<td", f.rows > 0 && (e += " rowspan='" + f.rows + "' "), f.cols > 0 && (e += " colspan='" + f.cols + "' "), e += "/>" + a(d).html() + "</td>")
                                            }
                                            count++;
                                        }),
                                    e += "</tr>"
                                //console.log(e)
                            }),


                        b.settings.exclude_img && (e = g(e)),
                        b.settings.exclude_links && (e = h(e)),
                        b.settings.exclude_inputs && (e = i(e)), b.tableRows.push(e)
                    }), b.tableToExcel(b.tableRows, b.settings.name, b.settings.sheetName)
            },
            tableToExcel: function (d, e, g) {
                var h, i, j, k = this, l = "";
                if (k.format = function (a, b) {
                    return a.replace(/{(\w+)}/g, function (a, c) {
                        return b[c]
                    })
                },
                    g = void 0 === g ? "Sheet" : g,
                    k.ctx = {
                        worksheet: e || "Worksheet", table: d, sheetName: g
                    },
                    l = k.template.head,
                    a.isArray(d)) for (h in d) l += k.template.sheet.head + g + h + k.template.sheet.tail;
                if (l += k.template.mid, a.isArray(d)) for (h in d) l += k.template.table.head + "{table" + h + "}" + k.template.table.tail;
                l += k.template.foot;
                for (h in d) k.ctx["table" + h] = d[h];
                if (delete k.ctx.table, !c.documentMode) {
                    var m = new Blob([k.format(l, k.ctx)],
                        {type: "application/vnd.ms-excel"});
                    b.URL = b.URL || b.webkitURL, i = b.URL.createObjectURL(m), j = c.createElement("a"), j.download = f(k.settings), j.href = i, c.body.appendChild(j), j.click(),
                        c.body.removeChild(j)
                } else if ("undefined" != typeof Blob) {
                    l = k.format(l, k.ctx), l = [l];
                    var n = new Blob(l, {type: "text/html"});
                    b.navigator.msSaveBlob(n, f(k.settings))
                } else txtArea1.document.open("text/html", "replace"),
                    txtArea1.document.write(k.format(l, k.ctx)),
                    txtArea1.document.close(),
                    txtArea1.focus(),
                    sa = txtArea1.document.execCommand("SaveAs", !0, f(k.settings));
                return !0
            }
        },
        a.fn[j] = function (b) {
            var c = this;
            return c.each(function () {
                a.data(c, "plugin_" + j) || a.data(c, "plugin_" + j, new e(this, b))
            }), c
        }
}(jQuery, window, document);
