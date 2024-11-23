import webpack from "webpack";
import {BuildOptions} from "./types/config";
import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import SpriteLoaderPlugin from 'svg-sprite-loader/plugin';
import {buildPagesList} from "./scripts/buildPagesList";
import WatchExternalFilesPlugin from 'webpack-watch-files-plugin'

export function buildPlugins({paths}: BuildOptions): webpack.WebpackPluginInstance[] {
    return [
        new MiniCssExtractPlugin({
            filename: 'css/[name].[contenthash:4].css',
        }),
        new webpack.ProgressPlugin(),
        // @ts-ignore
        new SpriteLoaderPlugin(),
        new WatchExternalFilesPlugin({
            files: ['src/**/*.html'],
        }),
        ...buildPagesList(paths),
    ];
}
