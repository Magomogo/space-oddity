const path = require('path'),
    wwwRoot = path.join(__dirname, '..', 'www');

module.exports = {
    entry: './src/index.js',
    output: {
        filename: 'bundle.js',
        path: wwwRoot
    },
    module: {
        rules: [
            { test: /\.js$/, exclude: /node_modules/, loader: "babel-loader" },
            { test: /\.css$/, use: [ 'style-loader', 'css-loader' ] },
            { test: /\.(woff2?|ttf|eot|svg)$/, loader: 'url-loader' },
        ]
    },
    devServer: {
        contentBase: wwwRoot,
        compress: true,
        port: 9000
    }
};
