!function(e){var t={};function n(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(r,o,function(t){return e[t]}.bind(null,o));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=71)}({0:function(e,t){!function(){e.exports=this.wp.element}()},1:function(e,t){!function(){e.exports=this.wp.primitives}()},13:function(e,t,n){var r=n(14);e.exports=function(e,t){if(e){if("string"==typeof e)return r(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);return"Object"===n&&e.constructor&&(n=e.constructor.name),"Map"===n||"Set"===n?Array.from(e):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?r(e,t):void 0}}},14:function(e,t){e.exports=function(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}},2:function(e,t){!function(){e.exports=this.wp.i18n}()},22:function(e,t){e.exports=function(e){if(Array.isArray(e))return e}},23:function(e,t){e.exports=function(e,t){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e)){var n=[],r=!0,o=!1,a=void 0;try{for(var c,i=e[Symbol.iterator]();!(r=(c=i.next()).done)&&(n.push(c.value),!t||n.length!==t);r=!0);}catch(e){o=!0,a=e}finally{try{r||null==i.return||i.return()}finally{if(o)throw a}}return n}}},24:function(e,t){e.exports=function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}},3:function(e,t){!function(){e.exports=this.wp.components}()},37:function(e,t){!function(){e.exports=this.wp.hooks}()},4:function(e,t){!function(){e.exports=this.wp.data}()},45:function(e,t,n){"use strict";var r=n(0),o=n(1),a=Object(r.createElement)(o.SVG,{viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg"},Object(r.createElement)(o.Path,{d:"M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"}));t.a=a},5:function(e,t){e.exports=function(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}},6:function(e,t,n){var r=n(22),o=n(23),a=n(13),c=n(24);e.exports=function(e,t){return r(e)||o(e,t)||a(e,t)||c()}},71:function(e,t,n){"use strict";n.r(t);var r=n(5),o=n.n(r),a=n(2),c=[{name:Object(a.__)("Fashion","nextgen"),slug:"fashion",value:""},{name:Object(a.__)("Home Decor","nextgen"),slug:"homedecor",value:""},{name:Object(a.__)("Coffee","nextgen"),slug:"coffee",value:""},{name:Object(a.__)("Construction","nextgen"),slug:"constructioncompany",value:""},{name:Object(a.__)("Art","nextgen"),slug:"personal_art",value:""},{name:Object(a.__)("Baking","nextgen"),slug:"bakeries",value:""},{name:Object(a.__)("Fitness","nextgen"),slug:"fitness",value:""},{name:Object(a.__)("Landscaping","nextgen"),slug:"landscaping",value:""}],i=n(4);function u(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function s(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?u(Object(n),!0).forEach((function(t){o()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):u(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var l={imageCategories:c||[],imageCategory:Object(i.select)("core").getEditedEntityRecord("root","site").imageCategory},g=(Object(i.registerStore)("nextgen/layout-selector",{reducer:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:l,t=arguments.length>1?arguments[1]:void 0;switch(t.type){case"UPDATE_IMAGE_CATEGORIES":return s(s({},e),{},{imageCategories:t.imageCategories,imageCategory:t.imageCategory})}return e},actions:{updateImageCategories:function(e){return{type:"UPDATE_IMAGE_CATEGORIES",imageCategories:e}},updateImageCategory:function(e){return{type:"UPDATE_IMAGE_CATEGORY",imageCategory:e}}},selectors:{getImageCategories:function(e){return e.imageCategories||[]},hasImageCategories:function(e){return!!e.imageCategories.length},getSelectedCategory:function(){return Object(i.select)("core").getEditedEntityRecord("root","site").image_category}}}),n(6)),f=n.n(g),p=n(0),m=n(8),b=n.n(m),y=n(9),d=n(45),v=n(3),O=Object(y.compose)([Object(i.withSelect)((function(e){var t=e("nextgen/layout-selector"),n=t.hasImageCategories,r=t.getImageCategories,o=t.getSelectedCategory;return{imageCategoriesEnabled:n(),imageCategories:r(),imageCategory:o()}})),Object(i.withDispatch)((function(e){return{saveEntityRecord:e("core").saveEntityRecord}}))])((function(e){var t,n=e.imageCategories,r=e.imageCategoriesEnabled,o=e.imageCategory,c=e.saveEntityRecord,i=Object(p.useState)(o),u=f()(i,2),s=u[0],l=u[1],g=null==n?void 0:n.filter((function(e){return e.slug===s})),m=(null==g||null===(t=g[0])||void 0===t?void 0:t.name)||s;return Object(p.useEffect)((function(){g.length&&c("root","site",{image_category:s})}),[s]),r?Object(p.createElement)(p.Fragment,null,Object(p.createElement)("span",{className:"coblocks-layout-selector__about"},Object(a.__)("My site is about:","nextgen")),Object(p.createElement)(v.Dropdown,{className:"coblocks-layout-selector__dropdown",renderToggle:function(e){var t=e.isOpen,n=e.onToggle;return Object(p.createElement)(v.Button,{isLink:!0,className:b()("coblocks-layout-selector__dropdown-button",{"is-open":t}),onClick:function(){return n()}},m,Object(p.createElement)(v.Icon,{icon:d.a,className:"chevron"}))},contentClassName:"coblocks-layout-selector__pop",renderContent:function(e){var t=e.onClose;return Object(p.createElement)(v.MenuGroup,null,n.map((function(e){var n=e.name,r=e.slug;return Object(p.createElement)(v.MenuItem,{key:"image-category-".concat(r),onClick:function(){l(r),t()}},n)})))}})):null})),j=n(37);Object(j.addFilter)("coblocks-layout-selector-controls","coblocks-image-category-selector",(function(e){return e.push(O),e}))},8:function(e,t,n){var r;!function(){"use strict";var n={}.hasOwnProperty;function o(){for(var e=[],t=0;t<arguments.length;t++){var r=arguments[t];if(r){var a=typeof r;if("string"===a||"number"===a)e.push(r);else if(Array.isArray(r)&&r.length){var c=o.apply(null,r);c&&e.push(c)}else if("object"===a)for(var i in r)n.call(r,i)&&r[i]&&e.push(i)}}return e.join(" ")}e.exports?(o.default=o,e.exports=o):void 0===(r=function(){return o}.apply(t,[]))||(e.exports=r)}()},9:function(e,t){!function(){e.exports=this.wp.compose}()}});