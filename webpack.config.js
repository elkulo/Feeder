/**
 * Webpack 5
 *
 * @version 2023.01.10
 */
const dirscript = "./public/webpack";

/* Plugins */
const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserJSPlugin = require("terser-webpack-plugin");
const ESLintPlugin = require("eslint-webpack-plugin");
const Webpack = require("webpack");

module.exports = {
  mode: "production",
  entry: {
    app: path.resolve(__dirname, dirscript, "./src/main.js"),
  },
  output: {
    path: path.resolve(__dirname, dirscript, "./dest"),
    filename: "[name].min.js",
    publicPath: "./",
    assetModuleFilename: "_output/[hash][ext]",
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ["@babel/preset-env"],
          },
        },
      },
      {
        test: /\.(sa|sc|c)ss$/i,
        use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
      },
      {
        test: /\.(gif|png|jpe?g|eot|wof|woff|woff2|ttf|svg)$/i,
        type: "asset",
        parser: {
          dataUrlCondition: {
            maxSize: 1024 * 100 /* 100KB以上のファイルは書き出し */,
          },
        },
      },
    ],
  },
  plugins: [
    new ESLintPlugin(),
    new MiniCssExtractPlugin({
      filename: "[name].min.css",
    }),
    new Webpack.DefinePlugin({
      __VUE_OPTIONS_API__: false,
      __VUE_PROD_DEVTOOLS__: false,
    }),
  ],
  optimization: {
    minimizer: [new TerserJSPlugin(), new CssMinimizerPlugin()],
  },
  performance: {
    maxEntrypointSize: 500000,
    maxAssetSize: 500000,
    assetFilter: function (assetFilename) {
      return assetFilename.endsWith(".js");
    },
  },
  resolve: {
    extensions: [".js"],
    alias: {
      vue$: "vue/dist/vue.esm-bundler.js",
    },
  },
};
