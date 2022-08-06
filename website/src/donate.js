import './util.js';

function loadDonateMaxList(limit) {
    $.ajax({
        url: 'https://www.imiphp.com/api/donate/donate/query?limit=' + limit + '&order=max',
        success: function (data) {
            for (var i = 0; i < 3; ++i) {
                var tr = $('#tr-' + (i + 1));
                var tds = tr.find('td');
                $(tds[0]).text((data.list[i].amount / 100).toFixed(2));
                if ('' === data.list[i].link) {
                    $(tds[1]).text(data.list[i].name);
                }
                else {
                    var link = $('<a target="_blank"></a>');
                    link.text(data.list[i].name).attr('href', data.list[i].link)
                    $(tds[1]).html(link)
                }
                $(tds[2]).text(data.list[i].message);
                if (data.list[i].time > 0) {
                    $(tds[3]).text(formatDate(data.list[i].time * 1000));
                }
                else {
                    $(tds[3]).text('-');
                }
            }
        },
        error: function () {
            alert('网络错误！');
        },
    })
}

function loadNewList(limit) {
    $.ajax({
        url: 'https://www.imiphp.com/api/donate/donate/query?limit=' + limit + '&order=new',
        success: function (data) {
            var tbody = $('#donate-table tbody');
            for (var i = 0; i < data.list.length; ++i) {
                var tr = $('<tr></tr>');
                var td = $('<td></td>');
                td.text((data.list[i].amount / 100).toFixed(2))
                tr.append(td)

                td = $('<td></td>');
                if ('' === data.list[i].link) {
                    td.text(data.list[i].name)
                }
                else {
                    var link = $('<a target="_blank"></a>');
                    link.text(data.list[i].name).attr('href', data.list[i].link)
                    td.html(link)
                }
                tr.append(td)

                td = $('<td></td>');
                td.text(data.list[i].message)
                tr.append(td)

                td = $('<td></td>');
                if (data.list[i].time > 0) {
                    td.text(formatDate(data.list[i].time * 1000))
                }
                else {
                    td.text('-');
                }
                tr.append(td)

                tbody.append(tr);
            }
        },
        error: function () {
            alert('网络错误！');
        },
    })
}

$(function () {
    if ($('#donate-table').length > 0) {
        var max = $('#donate-table').attr('imi-max');
        if (max > 0) {
            loadDonateMaxList(max);
        }
        var _new = $('#donate-table').attr('imi-new');
        if (_new > 0) {
            loadNewList(_new);
        }
    }
});
