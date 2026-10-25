const { when, whenDev, whenProd, whenTest, ESLINT_MODES, POSTCSS_MODES } = require("@craco/craco");
const path = require('path');
const webpack = require('webpack');

module.exports = {
    eslint: {
        configure: {
            globals: {
                '$': true,
                'CouponURLs': true,
                '__': true
            }
        }
    },
	webpack: {
		// NOTE
		// IN A REGULAR APP, ALIAS SHOULD BE UNDER RESOLVE
		// resolve {
		//   alias: {
		//   }
		// }
		// But CRACo is just weird, so we have to use it directly
		alias: {
			actions: path.resolve(__dirname, 'src/actions/'),
		},
        plugins: {
            add: [
                new webpack.ProvidePlugin({
                  $: [path.resolve(__dirname, 'src/globals.js'), '$'],
                  CouponsPlus: [path.resolve(__dirname, 'src/globals.js'), 'CouponURLs'],
                  __: [path.resolve(__dirname, 'src/globals.js'), '__'],
                })
            ]
        },
        configure: {
          optimization: {
            runtimeChunk: false,
            splitChunks: {
              chunks(chunk) {
                return false
              },
            },
          },
        },
	},
    babel: {
        "plugins": [
            [
                "@wordpress/babel-plugin-makepot",
                { "output": "../../../international/scripts-source.pot" }
            ]
        ]
    },
  // Extend/override the dev server configuration used by CRA
  // See: https://github.com/timarney/react-app-rewired#extended-configuration-options
  devServer: {
    //port: 8000,
	headers: {
	  "Access-Control-Allow-Origin": "http://localhost:8000",
	  "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, PATCH, OPTIONS",
	  "Access-Control-Allow-Headers": "X-Requested-With, content-type, Authorization",
	  "Access-Control-Allow-Credentials": "true"
	},
  }
};
