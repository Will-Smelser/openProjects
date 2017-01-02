module.exports = {
    entry: "./ui/js/example.js",
    output: {
        path: __dirname,
        filename: "./public/dist/js/bundle.js"
    },
    module: {
        loaders: [
            /*{ test: /\.css$/, loader: "style!css" }*/
            {
              test: /\.jsx?$/,
              exclude: /(node_modules|bower_components)/,
              loader: 'babel-loader',
              query: {
                presets: ['es2015', 'react']
              }
            }
          ]
    },
    //remove this if you want to use react
    resolve: {
        alias: {
            'react': 'preact-compat',
            'react-dom': 'preact-compat'
        }
    }
};