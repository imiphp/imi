/** 
 * 删除运行目录
 * by lovefc
**/
const fs = require('fs').promises
const path = require('path')
const save_dir = path.resolve(__dirname, '../dist');
async function rmdirAsync(filePath) {
  let stat = await fs.stat(filePath)
  if(stat.isFile()) {
    await fs.unlink(filePath)
  }else {
    let dirs = await fs.readdir(filePath)
    dirs = dirs.map(dir => rmdirAsync(path.join(filePath, dir)))
    await Promise.all(dirs)
    await fs.rmdir(filePath)
  }
}

rmdirAsync(save_dir).then(() => {
  console.log('删除成功');
})