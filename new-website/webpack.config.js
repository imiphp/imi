/*
 * @Author       : lovefc
 * @Date         : 2021-05-31 14:40:03
 * @LastEditTime : 2021-10-15 10:55:33
 */
const webpack = require('webpack');
const path = require('path');
const htmlWebpackPlugin = require('html-webpack-plugin');
const TerserPlugin = require("terser-webpack-plugin");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const glob = require('glob-all');
const PurifyCssPlugin = require('purgecss-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = {

    mode: 'development', //production
    entry: {
        'assgin/main': './src/main.js',
    },
    output: {
        path: __dirname + '/dist',
        filename: '[name].js',
    },
    module: {
        rules: [
            {
                test: /\.(htm|html)$/,
                loader: 'html-withimg-loader'
            },
            {
                test: /\.(sa|sc|c)ss$/,
                include: __dirname,
                exclude: /(node_modules)/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.(png|jpg|gif|svg)$/,
                loader: require.resolve('file-loader'),
                options: {
                    name: '[name].[ext]',
                    outputPath: 'assgin/images/',
                    esModule: false
                }
            },
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                loader: 'babel-loader',
                options: {
                    presets: ['@babel/preset-env'], // 声明兼容模式
                }
            },
            {
                test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
                include: __dirname,
                exclude: /(node_modules)/,
                loader: 'file-loader',
                options: {
                    limit: 100000,
                    outputPath: 'assgin/css/fonts/',
                    name: '[name].[hash:7].[ext]'
                }
            }
        ]
    },
    plugins: [
        new OptimizeCSSAssetsPlugin(), // 普通压缩
        // 消除无用css,该项会影响弹窗插件,会导致弹窗所用的css无法加载
        /*
        new PurifyCssPlugin({
            paths: glob.sync(path.join(__dirname, 'src/page/*.html'))
        }),	 	
        */
        new MiniCssExtractPlugin({
            filename: "assgin/css/main.css",
        }),
        new htmlWebpackPlugin({
            chunks: ['assgin/main'],
            inject: 'body',
            filename: 'index.html',
            template: 'src/page/index.html',
            favicon: 'src/page/favicon.ico',
            showErrors: false,
            minify: false,
            hash: true,
            isBrowser: false,
            isDevelopment: process.env.NODE_ENV !== 'production',
            nodeModules: process.env.NODE_ENV !== 'production' ? path.resolve(__dirname, '../node_modules') : false
        }),
        new htmlWebpackPlugin({
            chunks: ['assgin/main'],
            inject: 'body',
            filename: 'donate.html',
            template: 'src/page/donate.html',
            favicon: 'src/page/favicon.ico',
            showErrors: false,
            minify: false,
            hash: true,
            isBrowser: false,
            isDevelopment: process.env.NODE_ENV !== 'production',
            nodeModules: process.env.NODE_ENV !== 'production' ? path.resolve(__dirname, '../node_modules') : false
        }),
        new htmlWebpackPlugin({
            chunks: ['assgin/main'],
            inject: 'body',
            filename: 'case.html',
            template: 'src/page/case.html',
            favicon: 'src/page/favicon.ico',
            showErrors: false,
            minify: false,
            hash: true,
            isBrowser: false,
            isDevelopment: process.env.NODE_ENV !== 'production',
            nodeModules: process.env.NODE_ENV !== 'production' ? path.resolve(__dirname, '../node_modules') : false
        }),
        new CopyWebpackPlugin({
            patterns: [{ from: 'src/loadpage', to: 'assgin/loadpage' }]
        }),
    ],
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                include: /\.js(\?.*)?$/i,

                parallel: true,

                include: __dirname,

                exclude: /(node_modules)/,

                minify: undefined,

                //  是否将代码注释提取到一个单独的文件。
                //  属性值：Boolean | String | RegExp | Function<(node, comment) -> Boolean|Object> | Object
                //  默认为true， 只提取/^\**!|@preserve|@license|@cc_on/i注释
                //  感觉没什么特殊情况直接设置为false即可
                extractComments: true,

                //  压缩时的选项设置
                terserOptions: {}
            })
        ]
    }
};