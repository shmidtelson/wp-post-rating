(()=>{var t={564:function(t){t.exports=function(){var t={156:function(t){t.exports={name:"@romua1d/star-rating-js",version:"0.0.21",description:"Clean JavaScript Star Rating Library",main:"build/index.js",types:"build/types/index.d.ts",scripts:{start:"webpack serve --config webpack.config.demo.js",build:"webpack && tsc","build:demo":"webpack --config webpack.config.demo.js",test:"jest --silent",coverage:"npm run test -- --coverage",prepare:"npm run build",trypublish:"npm publish || true"},repository:{type:"git",url:"https://github.com/shmidtelson/star-rating-js"},author:"romua1d <admin@romua1d.ru>",license:"MIT",bugs:{url:"https://github.com/shmidtelson/star-rating-js/issues"},homepage:"https://shmidtelson.github.io/star-rating-js/",keywords:["stars","star","rating"],devDependencies:{"@babel/cli":"^7.14.5","@babel/core":"^7.14.6","@babel/plugin-proposal-class-properties":"^7.14.5","@babel/plugin-transform-typescript":"^7.14.6","@babel/polyfill":"^7.12.1","@babel/preset-env":"^7.14.7","@types/jest":"^26.0.24","@types/uuid":"^8.3.1","@typescript-eslint/eslint-plugin":"^4.28.2","@typescript-eslint/parser":"^4.28.2","babel-eslint":"^10.1.0","babel-loader":"^8.2.2","babel-preset-minify":"^0.5.1","css-loader":"^5.2.6",eslint:"^7.30.0","file-loader":"^6.2.0","html-webpack-plugin":"^5.3.2",jest:"^27.0.6","json-loader":"^0.5.7","mini-css-extract-plugin":"^2.1.0","optimize-css-assets-webpack-plugin":"^6.0.1",prettier:"^2.3.2","prettier-webpack-plugin":"^1.2.0",sass:"^1.35.2","sass-loader":"^12.1.0","style-loader":"^3.0.0","terser-webpack-plugin":"^5.1.4",tinycolor2:"^1.4.2",typescript:"^4.3.5","url-loader":"^4.1.1",uuid:"^8.3.2",webpack:"^5.44.0","webpack-cli":"^4.7.2","webpack-dev-server":"4.0.0-beta.3"},browser:{crypto:!1},jest:{roots:["<rootDir>/src"],collectCoverageFrom:["src/**/*.{js,jsx,ts,tsx}","!src/lib/index.js","!src/demo/index.js","!src/utils/**","!**/tests/**"],testMatch:["<rootDir>/src/**/__tests__/**/*.{js,jsx,ts,tsx}","<rootDir>/src/**/*.{spec,test}.{js,jsx,ts,tsx}"],transformIgnorePatterns:["[/\\\\]node_modules[/\\\\].+\\.(js|jsx|ts|tsx)$","^.+\\.module\\.(css|sass|scss)$"],modulePaths:["<rootDir>/src/"],moduleNameMapper:{"\\.(jpg|jpeg|png|gif|eot|otf|webp|svg|ttf|woff|woff2|mp4|webm|wav|mp3|m4a|aac|oga)$":"<rootDir>/scripts/testMock.js","\\.(css|less)$":"<rootDir>/scripts/testMock.js"},moduleFileExtensions:["web.js","js","web.ts","ts","web.tsx","tsx","json","web.jsx","jsx","node"]},dependencies:{}}},621:function(t,e,r){var n;!function(i){var a=/^\s+/,o=/\s+$/,s=0,l=i.round,c=i.min,u=i.max,f=i.random;function h(t,e){if(e=e||{},(t=t||"")instanceof h)return t;if(!(this instanceof h))return new h(t,e);var r=function(t){var e,r,n,s={r:0,g:0,b:0},l=1,f=null,h=null,d=null,g=!1,p=!1;return"string"==typeof t&&(t=function(t){t=t.replace(a,"").replace(o,"").toLowerCase();var e,r=!1;if(H[t])t=H[t],r=!0;else if("transparent"==t)return{r:0,g:0,b:0,a:0,format:"name"};return(e=$.rgb.exec(t))?{r:e[1],g:e[2],b:e[3]}:(e=$.rgba.exec(t))?{r:e[1],g:e[2],b:e[3],a:e[4]}:(e=$.hsl.exec(t))?{h:e[1],s:e[2],l:e[3]}:(e=$.hsla.exec(t))?{h:e[1],s:e[2],l:e[3],a:e[4]}:(e=$.hsv.exec(t))?{h:e[1],s:e[2],v:e[3]}:(e=$.hsva.exec(t))?{h:e[1],s:e[2],v:e[3],a:e[4]}:(e=$.hex8.exec(t))?{r:N(e[1]),g:N(e[2]),b:N(e[3]),a:I(e[4]),format:r?"name":"hex8"}:(e=$.hex6.exec(t))?{r:N(e[1]),g:N(e[2]),b:N(e[3]),format:r?"name":"hex"}:(e=$.hex4.exec(t))?{r:N(e[1]+""+e[1]),g:N(e[2]+""+e[2]),b:N(e[3]+""+e[3]),a:I(e[4]+""+e[4]),format:r?"name":"hex8"}:!!(e=$.hex3.exec(t))&&{r:N(e[1]+""+e[1]),g:N(e[2]+""+e[2]),b:N(e[3]+""+e[3]),format:r?"name":"hex"}}(t)),"object"==typeof t&&(V(t.r)&&V(t.g)&&V(t.b)?(e=t.r,r=t.g,n=t.b,s={r:255*q(e,255),g:255*q(r,255),b:255*q(n,255)},g=!0,p="%"===String(t.r).substr(-1)?"prgb":"rgb"):V(t.h)&&V(t.s)&&V(t.v)?(f=M(t.s),h=M(t.v),s=function(t,e,r){t=6*q(t,360),e=q(e,100),r=q(r,100);var n=i.floor(t),a=t-n,o=r*(1-e),s=r*(1-a*e),l=r*(1-(1-a)*e),c=n%6;return{r:255*[r,s,o,o,l,r][c],g:255*[l,r,r,s,o,o][c],b:255*[o,o,l,r,r,s][c]}}(t.h,f,h),g=!0,p="hsv"):V(t.h)&&V(t.s)&&V(t.l)&&(f=M(t.s),d=M(t.l),s=function(t,e,r){var n,i,a;function o(t,e,r){return r<0&&(r+=1),r>1&&(r-=1),r<1/6?t+6*(e-t)*r:r<.5?e:r<2/3?t+(e-t)*(2/3-r)*6:t}if(t=q(t,360),e=q(e,100),r=q(r,100),0===e)n=i=a=r;else{var s=r<.5?r*(1+e):r+e-r*e,l=2*r-s;n=o(l,s,t+1/3),i=o(l,s,t),a=o(l,s,t-1/3)}return{r:255*n,g:255*i,b:255*a}}(t.h,f,d),g=!0,p="hsl"),t.hasOwnProperty("a")&&(l=t.a)),l=F(l),{ok:g,format:t.format||p,r:c(255,u(s.r,0)),g:c(255,u(s.g,0)),b:c(255,u(s.b,0)),a:l}}(t);this._originalInput=t,this._r=r.r,this._g=r.g,this._b=r.b,this._a=r.a,this._roundA=l(100*this._a)/100,this._format=e.format||r.format,this._gradientType=e.gradientType,this._r<1&&(this._r=l(this._r)),this._g<1&&(this._g=l(this._g)),this._b<1&&(this._b=l(this._b)),this._ok=r.ok,this._tc_id=s++}function d(t,e,r){t=q(t,255),e=q(e,255),r=q(r,255);var n,i,a=u(t,e,r),o=c(t,e,r),s=(a+o)/2;if(a==o)n=i=0;else{var l=a-o;switch(i=s>.5?l/(2-a-o):l/(a+o),a){case t:n=(e-r)/l+(e<r?6:0);break;case e:n=(r-t)/l+2;break;case r:n=(t-e)/l+4}n/=6}return{h:n,s:i,l:s}}function g(t,e,r){t=q(t,255),e=q(e,255),r=q(r,255);var n,i,a=u(t,e,r),o=c(t,e,r),s=a,l=a-o;if(i=0===a?0:l/a,a==o)n=0;else{switch(a){case t:n=(e-r)/l+(e<r?6:0);break;case e:n=(r-t)/l+2;break;case r:n=(t-e)/l+4}n/=6}return{h:n,s:i,v:s}}function p(t,e,r,n){var i=[O(l(t).toString(16)),O(l(e).toString(16)),O(l(r).toString(16))];return n&&i[0].charAt(0)==i[0].charAt(1)&&i[1].charAt(0)==i[1].charAt(1)&&i[2].charAt(0)==i[2].charAt(1)?i[0].charAt(0)+i[1].charAt(0)+i[2].charAt(0):i.join("")}function b(t,e,r,n){return[O(T(n)),O(l(t).toString(16)),O(l(e).toString(16)),O(l(r).toString(16))].join("")}function v(t,e){e=0===e?0:e||10;var r=h(t).toHsl();return r.s-=e/100,r.s=z(r.s),h(r)}function m(t,e){e=0===e?0:e||10;var r=h(t).toHsl();return r.s+=e/100,r.s=z(r.s),h(r)}function y(t){return h(t).desaturate(100)}function _(t,e){e=0===e?0:e||10;var r=h(t).toHsl();return r.l+=e/100,r.l=z(r.l),h(r)}function k(t,e){e=0===e?0:e||10;var r=h(t).toRgb();return r.r=u(0,c(255,r.r-l(-e/100*255))),r.g=u(0,c(255,r.g-l(-e/100*255))),r.b=u(0,c(255,r.b-l(-e/100*255))),h(r)}function w(t,e){e=0===e?0:e||10;var r=h(t).toHsl();return r.l-=e/100,r.l=z(r.l),h(r)}function x(t,e){var r=h(t).toHsl(),n=(r.h+e)%360;return r.h=n<0?360+n:n,h(r)}function C(t){var e=h(t).toHsl();return e.h=(e.h+180)%360,h(e)}function A(t){var e=h(t).toHsl(),r=e.h;return[h(t),h({h:(r+120)%360,s:e.s,l:e.l}),h({h:(r+240)%360,s:e.s,l:e.l})]}function S(t){var e=h(t).toHsl(),r=e.h;return[h(t),h({h:(r+90)%360,s:e.s,l:e.l}),h({h:(r+180)%360,s:e.s,l:e.l}),h({h:(r+270)%360,s:e.s,l:e.l})]}function j(t){var e=h(t).toHsl(),r=e.h;return[h(t),h({h:(r+72)%360,s:e.s,l:e.l}),h({h:(r+216)%360,s:e.s,l:e.l})]}function R(t,e,r){e=e||6,r=r||30;var n=h(t).toHsl(),i=360/r,a=[h(t)];for(n.h=(n.h-(i*e>>1)+720)%360;--e;)n.h=(n.h+i)%360,a.push(h(n));return a}function P(t,e){e=e||6;for(var r=h(t).toHsv(),n=r.h,i=r.s,a=r.v,o=[],s=1/e;e--;)o.push(h({h:n,s:i,v:a})),a=(a+s)%1;return o}h.prototype={isDark:function(){return this.getBrightness()<128},isLight:function(){return!this.isDark()},isValid:function(){return this._ok},getOriginalInput:function(){return this._originalInput},getFormat:function(){return this._format},getAlpha:function(){return this._a},getBrightness:function(){var t=this.toRgb();return(299*t.r+587*t.g+114*t.b)/1e3},getLuminance:function(){var t,e,r,n=this.toRgb();return t=n.r/255,e=n.g/255,r=n.b/255,.2126*(t<=.03928?t/12.92:i.pow((t+.055)/1.055,2.4))+.7152*(e<=.03928?e/12.92:i.pow((e+.055)/1.055,2.4))+.0722*(r<=.03928?r/12.92:i.pow((r+.055)/1.055,2.4))},setAlpha:function(t){return this._a=F(t),this._roundA=l(100*this._a)/100,this},toHsv:function(){var t=g(this._r,this._g,this._b);return{h:360*t.h,s:t.s,v:t.v,a:this._a}},toHsvString:function(){var t=g(this._r,this._g,this._b),e=l(360*t.h),r=l(100*t.s),n=l(100*t.v);return 1==this._a?"hsv("+e+", "+r+"%, "+n+"%)":"hsva("+e+", "+r+"%, "+n+"%, "+this._roundA+")"},toHsl:function(){var t=d(this._r,this._g,this._b);return{h:360*t.h,s:t.s,l:t.l,a:this._a}},toHslString:function(){var t=d(this._r,this._g,this._b),e=l(360*t.h),r=l(100*t.s),n=l(100*t.l);return 1==this._a?"hsl("+e+", "+r+"%, "+n+"%)":"hsla("+e+", "+r+"%, "+n+"%, "+this._roundA+")"},toHex:function(t){return p(this._r,this._g,this._b,t)},toHexString:function(t){return"#"+this.toHex(t)},toHex8:function(t){return function(t,e,r,n,i){var a=[O(l(t).toString(16)),O(l(e).toString(16)),O(l(r).toString(16)),O(T(n))];return i&&a[0].charAt(0)==a[0].charAt(1)&&a[1].charAt(0)==a[1].charAt(1)&&a[2].charAt(0)==a[2].charAt(1)&&a[3].charAt(0)==a[3].charAt(1)?a[0].charAt(0)+a[1].charAt(0)+a[2].charAt(0)+a[3].charAt(0):a.join("")}(this._r,this._g,this._b,this._a,t)},toHex8String:function(t){return"#"+this.toHex8(t)},toRgb:function(){return{r:l(this._r),g:l(this._g),b:l(this._b),a:this._a}},toRgbString:function(){return 1==this._a?"rgb("+l(this._r)+", "+l(this._g)+", "+l(this._b)+")":"rgba("+l(this._r)+", "+l(this._g)+", "+l(this._b)+", "+this._roundA+")"},toPercentageRgb:function(){return{r:l(100*q(this._r,255))+"%",g:l(100*q(this._g,255))+"%",b:l(100*q(this._b,255))+"%",a:this._a}},toPercentageRgbString:function(){return 1==this._a?"rgb("+l(100*q(this._r,255))+"%, "+l(100*q(this._g,255))+"%, "+l(100*q(this._b,255))+"%)":"rgba("+l(100*q(this._r,255))+"%, "+l(100*q(this._g,255))+"%, "+l(100*q(this._b,255))+"%, "+this._roundA+")"},toName:function(){return 0===this._a?"transparent":!(this._a<1)&&(E[p(this._r,this._g,this._b,!0)]||!1)},toFilter:function(t){var e="#"+b(this._r,this._g,this._b,this._a),r=e,n=this._gradientType?"GradientType = 1, ":"";if(t){var i=h(t);r="#"+b(i._r,i._g,i._b,i._a)}return"progid:DXImageTransform.Microsoft.gradient("+n+"startColorstr="+e+",endColorstr="+r+")"},toString:function(t){var e=!!t;t=t||this._format;var r=!1,n=this._a<1&&this._a>=0;return e||!n||"hex"!==t&&"hex6"!==t&&"hex3"!==t&&"hex4"!==t&&"hex8"!==t&&"name"!==t?("rgb"===t&&(r=this.toRgbString()),"prgb"===t&&(r=this.toPercentageRgbString()),"hex"!==t&&"hex6"!==t||(r=this.toHexString()),"hex3"===t&&(r=this.toHexString(!0)),"hex4"===t&&(r=this.toHex8String(!0)),"hex8"===t&&(r=this.toHex8String()),"name"===t&&(r=this.toName()),"hsl"===t&&(r=this.toHslString()),"hsv"===t&&(r=this.toHsvString()),r||this.toHexString()):"name"===t&&0===this._a?this.toName():this.toRgbString()},clone:function(){return h(this.toString())},_applyModification:function(t,e){var r=t.apply(null,[this].concat([].slice.call(e)));return this._r=r._r,this._g=r._g,this._b=r._b,this.setAlpha(r._a),this},lighten:function(){return this._applyModification(_,arguments)},brighten:function(){return this._applyModification(k,arguments)},darken:function(){return this._applyModification(w,arguments)},desaturate:function(){return this._applyModification(v,arguments)},saturate:function(){return this._applyModification(m,arguments)},greyscale:function(){return this._applyModification(y,arguments)},spin:function(){return this._applyModification(x,arguments)},_applyCombination:function(t,e){return t.apply(null,[this].concat([].slice.call(e)))},analogous:function(){return this._applyCombination(R,arguments)},complement:function(){return this._applyCombination(C,arguments)},monochromatic:function(){return this._applyCombination(P,arguments)},splitcomplement:function(){return this._applyCombination(j,arguments)},triad:function(){return this._applyCombination(A,arguments)},tetrad:function(){return this._applyCombination(S,arguments)}},h.fromRatio=function(t,e){if("object"==typeof t){var r={};for(var n in t)t.hasOwnProperty(n)&&(r[n]="a"===n?t[n]:M(t[n]));t=r}return h(t,e)},h.equals=function(t,e){return!(!t||!e)&&h(t).toRgbString()==h(e).toRgbString()},h.random=function(){return h.fromRatio({r:f(),g:f(),b:f()})},h.mix=function(t,e,r){r=0===r?0:r||50;var n=h(t).toRgb(),i=h(e).toRgb(),a=r/100;return h({r:(i.r-n.r)*a+n.r,g:(i.g-n.g)*a+n.g,b:(i.b-n.b)*a+n.b,a:(i.a-n.a)*a+n.a})},h.readability=function(t,e){var r=h(t),n=h(e);return(i.max(r.getLuminance(),n.getLuminance())+.05)/(i.min(r.getLuminance(),n.getLuminance())+.05)},h.isReadable=function(t,e,r){var n,i,a=h.readability(t,e);switch(i=!1,(n=function(t){var e,r;return"AA"!==(e=((t=t||{level:"AA",size:"small"}).level||"AA").toUpperCase())&&"AAA"!==e&&(e="AA"),"small"!==(r=(t.size||"small").toLowerCase())&&"large"!==r&&(r="small"),{level:e,size:r}}(r)).level+n.size){case"AAsmall":case"AAAlarge":i=a>=4.5;break;case"AAlarge":i=a>=3;break;case"AAAsmall":i=a>=7}return i},h.mostReadable=function(t,e,r){var n,i,a,o,s=null,l=0;i=(r=r||{}).includeFallbackColors,a=r.level,o=r.size;for(var c=0;c<e.length;c++)(n=h.readability(t,e[c]))>l&&(l=n,s=h(e[c]));return h.isReadable(t,s,{level:a,size:o})||!i?s:(r.includeFallbackColors=!1,h.mostReadable(t,["#fff","#000"],r))};var H=h.names={aliceblue:"f0f8ff",antiquewhite:"faebd7",aqua:"0ff",aquamarine:"7fffd4",azure:"f0ffff",beige:"f5f5dc",bisque:"ffe4c4",black:"000",blanchedalmond:"ffebcd",blue:"00f",blueviolet:"8a2be2",brown:"a52a2a",burlywood:"deb887",burntsienna:"ea7e5d",cadetblue:"5f9ea0",chartreuse:"7fff00",chocolate:"d2691e",coral:"ff7f50",cornflowerblue:"6495ed",cornsilk:"fff8dc",crimson:"dc143c",cyan:"0ff",darkblue:"00008b",darkcyan:"008b8b",darkgoldenrod:"b8860b",darkgray:"a9a9a9",darkgreen:"006400",darkgrey:"a9a9a9",darkkhaki:"bdb76b",darkmagenta:"8b008b",darkolivegreen:"556b2f",darkorange:"ff8c00",darkorchid:"9932cc",darkred:"8b0000",darksalmon:"e9967a",darkseagreen:"8fbc8f",darkslateblue:"483d8b",darkslategray:"2f4f4f",darkslategrey:"2f4f4f",darkturquoise:"00ced1",darkviolet:"9400d3",deeppink:"ff1493",deepskyblue:"00bfff",dimgray:"696969",dimgrey:"696969",dodgerblue:"1e90ff",firebrick:"b22222",floralwhite:"fffaf0",forestgreen:"228b22",fuchsia:"f0f",gainsboro:"dcdcdc",ghostwhite:"f8f8ff",gold:"ffd700",goldenrod:"daa520",gray:"808080",green:"008000",greenyellow:"adff2f",grey:"808080",honeydew:"f0fff0",hotpink:"ff69b4",indianred:"cd5c5c",indigo:"4b0082",ivory:"fffff0",khaki:"f0e68c",lavender:"e6e6fa",lavenderblush:"fff0f5",lawngreen:"7cfc00",lemonchiffon:"fffacd",lightblue:"add8e6",lightcoral:"f08080",lightcyan:"e0ffff",lightgoldenrodyellow:"fafad2",lightgray:"d3d3d3",lightgreen:"90ee90",lightgrey:"d3d3d3",lightpink:"ffb6c1",lightsalmon:"ffa07a",lightseagreen:"20b2aa",lightskyblue:"87cefa",lightslategray:"789",lightslategrey:"789",lightsteelblue:"b0c4de",lightyellow:"ffffe0",lime:"0f0",limegreen:"32cd32",linen:"faf0e6",magenta:"f0f",maroon:"800000",mediumaquamarine:"66cdaa",mediumblue:"0000cd",mediumorchid:"ba55d3",mediumpurple:"9370db",mediumseagreen:"3cb371",mediumslateblue:"7b68ee",mediumspringgreen:"00fa9a",mediumturquoise:"48d1cc",mediumvioletred:"c71585",midnightblue:"191970",mintcream:"f5fffa",mistyrose:"ffe4e1",moccasin:"ffe4b5",navajowhite:"ffdead",navy:"000080",oldlace:"fdf5e6",olive:"808000",olivedrab:"6b8e23",orange:"ffa500",orangered:"ff4500",orchid:"da70d6",palegoldenrod:"eee8aa",palegreen:"98fb98",paleturquoise:"afeeee",palevioletred:"db7093",papayawhip:"ffefd5",peachpuff:"ffdab9",peru:"cd853f",pink:"ffc0cb",plum:"dda0dd",powderblue:"b0e0e6",purple:"800080",rebeccapurple:"663399",red:"f00",rosybrown:"bc8f8f",royalblue:"4169e1",saddlebrown:"8b4513",salmon:"fa8072",sandybrown:"f4a460",seagreen:"2e8b57",seashell:"fff5ee",sienna:"a0522d",silver:"c0c0c0",skyblue:"87ceeb",slateblue:"6a5acd",slategray:"708090",slategrey:"708090",snow:"fffafa",springgreen:"00ff7f",steelblue:"4682b4",tan:"d2b48c",teal:"008080",thistle:"d8bfd8",tomato:"ff6347",turquoise:"40e0d0",violet:"ee82ee",wheat:"f5deb3",white:"fff",whitesmoke:"f5f5f5",yellow:"ff0",yellowgreen:"9acd32"},E=h.hexNames=function(t){var e={};for(var r in t)t.hasOwnProperty(r)&&(e[t[r]]=r);return e}(H);function F(t){return t=parseFloat(t),(isNaN(t)||t<0||t>1)&&(t=1),t}function q(t,e){(function(t){return"string"==typeof t&&-1!=t.indexOf(".")&&1===parseFloat(t)})(t)&&(t="100%");var r=function(t){return"string"==typeof t&&-1!=t.indexOf("%")}(t);return t=c(e,u(0,parseFloat(t))),r&&(t=parseInt(t*e,10)/100),i.abs(t-e)<1e-6?1:t%e/parseFloat(e)}function z(t){return c(1,u(0,t))}function N(t){return parseInt(t,16)}function O(t){return 1==t.length?"0"+t:""+t}function M(t){return t<=1&&(t=100*t+"%"),t}function T(t){return i.round(255*parseFloat(t)).toString(16)}function I(t){return N(t)/255}var B,L,D,$=(L="[\\s|\\(]+("+(B="(?:[-\\+]?\\d*\\.\\d+%?)|(?:[-\\+]?\\d+%?)")+")[,|\\s]+("+B+")[,|\\s]+("+B+")\\s*\\)?",D="[\\s|\\(]+("+B+")[,|\\s]+("+B+")[,|\\s]+("+B+")[,|\\s]+("+B+")\\s*\\)?",{CSS_UNIT:new RegExp(B),rgb:new RegExp("rgb"+L),rgba:new RegExp("rgba"+D),hsl:new RegExp("hsl"+L),hsla:new RegExp("hsla"+D),hsv:new RegExp("hsv"+L),hsva:new RegExp("hsva"+D),hex3:/^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,hex6:/^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,hex4:/^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,hex8:/^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/});function V(t){return!!$.CSS_UNIT.exec(t)}t.exports?t.exports=h:void 0===(n=function(){return h}.call(e,r,e,t))||(t.exports=n)}(Math)}},e={};function r(n){var i=e[n];if(void 0!==i)return i.exports;var a=e[n]={exports:{}};return t[n](a,a.exports,r),a.exports}r.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return r.d(e,{a:e}),e},r.d=function(t,e){for(var n in e)r.o(e,n)&&!r.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:e[n]})},r.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},r.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})};var n={};return function(){"use strict";var t;r.r(n),r.d(n,{default:function(){return R}});var e=new Uint8Array(16);function i(){if(!t&&!(t="undefined"!=typeof crypto&&crypto.getRandomValues&&crypto.getRandomValues.bind(crypto)||"undefined"!=typeof msCrypto&&"function"==typeof msCrypto.getRandomValues&&msCrypto.getRandomValues.bind(msCrypto)))throw new Error("crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported");return t(e)}for(var a=/^(?:[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}|00000000-0000-0000-0000-000000000000)$/i,o=function(t){return"string"==typeof t&&a.test(t)},s=[],l=0;l<256;++l)s.push((l+256).toString(16).substr(1));var c=function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,r=(s[t[e+0]]+s[t[e+1]]+s[t[e+2]]+s[t[e+3]]+"-"+s[t[e+4]]+s[t[e+5]]+"-"+s[t[e+6]]+s[t[e+7]]+"-"+s[t[e+8]]+s[t[e+9]]+"-"+s[t[e+10]]+s[t[e+11]]+s[t[e+12]]+s[t[e+13]]+s[t[e+14]]+s[t[e+15]]).toLowerCase();if(!o(r))throw TypeError("Stringified UUID is invalid");return r},u=function(t,e,r){var n=(t=t||{}).random||(t.rng||i)();if(n[6]=15&n[6]|64,n[8]=63&n[8]|128,e){r=r||0;for(var a=0;a<16;++a)e[r+a]=n[a];return e}return c(n)};function f(t){return/^#[0-9A-F]{3,6}$/i.test(t)}function h(t){console.error("[StarRating library]: ".concat(t))}function d(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function g(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var p=r(621),b=function(){function t(e){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),g(this,"_currentRating",5),g(this,"_starsColorPrimary","#ff0000"),g(this,"_starsColorHover","#cc0000"),g(this,"_uniqueClassName",null),g(this,"_disabled",!1),g(this,"_size","16px"),g(this,"_loader",!1),g(this,"_message",""),g(this,"_textColor","#848484"),g(this,"_infoPanelBackgroundColor","#ffffff"),"starsColor"in e&&(this.starsColorPrimary=e.starsColor),"currentRating"in e&&(this.currentRating=e.currentRating),"disabled"in e&&(this.disabled=e.disabled),"size"in e&&(this.size=e.size),"loader"in e&&(this.loader=e.loader),"message"in e&&(this.message=e.message),"textColor"in e&&(this.textColor=e.textColor),"infoPanelBackgroundColor"in e&&(this.infoPanelBackgroundColor=e.infoPanelBackgroundColor),this.uniqueClassName="stars-rating--".concat(u())}var e,r,n;return e=t,(r=[{key:"starsColorPrimary",get:function(){return this._starsColorPrimary},set:function(t){f(t)?(this._starsColorPrimary=t,this._starsColorHover=p(t).darken(10).toString()):h(" Error with validation hex color, current value is ".concat(t,", but i wait for example #000"))}},{key:"starsColorHover",get:function(){return this._starsColorHover}},{key:"currentRating",get:function(){return this._currentRating},set:function(t){t%1==0&&t<6&&t>=0?this._currentRating=t:h("You must use params from 0 to 5, current value is ".concat(t))}},{key:"uniqueClassName",get:function(){return this._uniqueClassName},set:function(t){this._uniqueClassName=t}},{key:"disabled",get:function(){return this._disabled},set:function(t){this._disabled=t}},{key:"size",get:function(){return this._size},set:function(t){this._size=t}},{key:"loader",get:function(){return this._loader},set:function(t){this._loader=t}},{key:"message",get:function(){return this._message},set:function(t){this._message=t}},{key:"textColor",get:function(){return this._textColor},set:function(t){f(t)?this._textColor=t:h(" Error with validation hex color, current value is ".concat(t,", but i wait for example #000"))}},{key:"infoPanelBackgroundColor",get:function(){return this._infoPanelBackgroundColor},set:function(t){f(t)?this._infoPanelBackgroundColor=t:h(" Error with validation hex color, current value is ".concat(t,", but i wait for example #000"))}}])&&d(e.prototype,r),n&&d(e,n),t}();function v(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function m(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var y=function(){function t(e,r){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),m(this,"options",void 0),m(this,"htmlElement",void 0),this.options=e,this.htmlElement=r}var e,r,n;return e=t,(r=[{key:"renderStars",value:function(){this.htmlElement.innerHTML='\n      <div class="stars-rating'.concat(this.options.disabled?"":" hoverable"," ").concat(this.options.uniqueClassName,'">\n      <div class="stars-rating--content">\n        ').concat(this._renderSpans(this.options.currentRating),"\n        ").concat(this._renderSpin(),"\n      </div>\n      ").concat(this._renderInfoPanel(),"\n      <style>\n        .").concat(this.options.uniqueClassName," {\n          color: ").concat(this.options.starsColorPrimary,";\n          font-size: ").concat(this.options.size,";\n        }\n        .").concat(this.options.uniqueClassName," .stars-rating--info-panel {\n          color: ").concat(this.options.textColor,";\n          border: 1px solid ").concat(this.options.textColor,";\n          background: ").concat(this.options.infoPanelBackgroundColor,";\n        }\n        .").concat(this.options.uniqueClassName," .stars-rating--info-panel:before {\n          border-right-color: ").concat(this.options.textColor,"\n        }\n        .").concat(this.options.uniqueClassName," .stars-rating--info-panel:after {\n          border-right-color: ").concat(this.options.infoPanelBackgroundColor,";\n        }\n        .").concat(this.options.uniqueClassName," .icon-star{\n          font-size: ").concat(this.options.size,";\n        }\n        .").concat(this.options.uniqueClassName,".hoverable .icon-star:hover ~ .icon-star:before,\n        .").concat(this.options.uniqueClassName,".hoverable .icon-star:hover:before {\n          color: ").concat(this.options.starsColorHover,";\n        }\n      </style>\n    </div>\n    ")}},{key:"_renderInfoPanel",value:function(){return this.options.loader?"":""!==this.options.message?'\n        <div class="stars-rating--info-panel">'.concat(this.options.message,"</div>\n      "):""}},{key:"_renderSpin",value:function(){return this.options.loader?'\n    <div class="wpr-rating-loader wpr-hide">\n      <i class="icon-spin6 animate-spin"></i>\n    </div>\n':""}},{key:"_renderSpans",value:function(t){if(this.options.loader)return"";t="string"==typeof t?parseInt(t):t;var e=Array.from(Array(5).keys()),r="";return e.reverse().forEach((function(e){var n=e+1;r+='\n        <span\n          class="icon-star'.concat(t===n?" checked":"",'"\n          data-value="').concat(n,'"\n        ></span>')})),r}}])&&v(e.prototype,r),n&&v(e,n),t}();function _(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function k(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var w=function(){function t(e,r){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),k(this,"htmlElement",void 0),k(this,"starRating",void 0),this.htmlElement=e,this.starRating=r}var e,r,n;return e=t,(r=[{key:"init",value:function(){var t=this;setTimeout((function(){t.clickEventInit()}),50)}},{key:"clickEventInit",value:function(){for(var t=this,e=this.htmlElement.querySelectorAll(".icon-star"),r=0;r<e.length;r++)e[r].addEventListener("click",(function(e){t.starRating.onChange(e)}))}}])&&_(e.prototype,r),n&&_(e,n),t}(),x=r(156),C=r.n(x);function A(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function S(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function j(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var R=function(){function t(e){var r=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};A(this,t),j(this,"view",void 0),j(this,"options",void 0),j(this,"events",void 0),j(this,"version",void 0),this.version=C().version,this.options=new b(r),this.view=new y(this.options,e),this.events=new w(e,this),this.init()}var e,r,n;return e=t,(r=[{key:"init",value:function(){this.view.renderStars(),this.events.init()}},{key:"changeRatingValue",value:function(t){this.options.currentRating=t,this.init()}},{key:"disable",value:function(){this.options.disabled=!0,this.init()}},{key:"enable",value:function(){this.options.disabled=!1,this.init()}},{key:"changeColor",value:function(t){this.options.starsColorPrimary=t,this.view.renderStars()}},{key:"changeSize",value:function(t){this.options.size=t,this.view.renderStars()}},{key:"changeLoader",value:function(){this.options.loader=!this.options.loader,this.view.renderStars()}},{key:"changeMessage",value:function(t){this.options.message=t,this.view.renderStars()}},{key:"changeTextColor",value:function(t){this.options.textColor=t,this.view.renderStars()}},{key:"changeInfoPanelBackgroundColor",value:function(t){this.options.infoPanelBackgroundColor=t,this.view.renderStars()}},{key:"onChange",value:function(t){var e,r;this.options.disabled||null!=t&&null!==(e=t.target)&&void 0!==e&&null!==(r=e.dataset)&&void 0!==r&&r.value&&this.changeRatingValue(t.target.dataset.value)}}])&&S(e.prototype,r),n&&S(e,n),t}()}(),n}()}},e={};function r(n){var i=e[n];if(void 0!==i)return i.exports;var a=e[n]={exports:{}};return t[n].call(a.exports,a,a.exports,r),a.exports}r.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return r.d(e,{a:e}),e},r.d=(t,e)=>{for(var n in e)r.o(e,n)&&!r.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:e[n]})},r.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),(()=>{"use strict";var t=r(564),e=r.n(t);jQuery(document).ready((function(t){var r=new(e())(document.querySelectorAll(".wpr-wrapp")[0],{message:"(5)",value:4,size:"30px"}),n=t(".color_chooser_js"),i=t(".text_color_chooser_js"),a=t(".text_background_color_chooser_js"),o=function(){r.changeColor(n.val()),r.changeTextColor(i.val()),r.changeInfoPanelBackgroundColor(a.val())};o();var s={change:function(){o()}};n.wpColorPicker(s),i.wpColorPicker(s),a.wpColorPicker(s)}))})()})();