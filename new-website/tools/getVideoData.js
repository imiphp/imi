/** 
 * 生成b站视频列表数据 
 * by lovefc
**/
const path = require('path');
const axios = require('axios');
const fs = require('fs');
const list = {};
const vurl = 'https://www.bilibili.com/video/';
const save_dir = path.resolve(__dirname, '../dist/video');
const urlib = require("url");

fs.mkdir(save_dir, { recursive: true }, (err) => {
    if (err) throw err;
});

fs.mkdir(save_dir + '/images/', { recursive: true }, (err) => {
    if (err) throw err;
});

function saveImg(imgurl, save_dir) {
    let filename = path.basename(imgurl);
    let save_filename = save_dir + '/images/' + filename;
    // 获取远端图片
    axios({
        method: 'get',
        url: imgurl,
        responseType: 'stream'
    })
        .then(function (response) {
            response.data.pipe(fs.createWriteStream(save_filename))
        }).catch(function (error) {
            console.log(error);
        });
}

axios.get('https://api.bilibili.com/x/space/arc/search?mid=768718&ps=30&tid=36&pn=1&keyword=&order=pubdate&jsonp=jsonp')
    .then(function (response) {
        let vlist = response.data.data.list.vlist;
        for (let k in vlist) {
            list[k] = {};
			let filename = path.basename(vlist[k]['pic']);
            list[k]['title'] = vlist[k]['title'],
                list[k]['description'] = vlist[k]['description'],
                list[k]['pic'] = './video/images/'+filename,
                list[k]['url'] = vurl + vlist[k]['bvid'];
            saveImg(vlist[k]['pic'], save_dir);
        }

        fs.writeFile(save_dir + '/data.json', JSON.stringify(list), 'utf8', function (err) {
            //如果err=null，表示文件使用成功，否则，表示希尔文件失败
            if (err)
                console.log('写文件出错了，错误是：' + err);
            else
                console.log('ok');
        })
    }).catch(function (error) {
        console.log(error);
    });