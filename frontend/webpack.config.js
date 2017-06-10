var path = require('path');

module.exports = {
    entry: './src/index.js',
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'www')
    },
    module: {
        rules: [
            { test: /\.js$/, exclude: /node_modules/, loader: "babel-loader" },
            { test: /\.css$/, use: [ 'style-loader', 'css-loader' ] },
            { test: /\.(woff2?|ttf|eot|svg)$/, loader: 'url-loader' },
        ]
    },
    devServer: {
        contentBase: path.join(__dirname, "www"),
        compress: true,
        port: 9000
    }
};
