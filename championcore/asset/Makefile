
.PHONY:
directories: dist/vendor/baguettebox.js dist/vendor/croppie dist/vendor/masonry-layout dist/vendor/mediaelement dist/vendor/moment dist/vendor/pikaday dist/vendor/pikaday-time dist/vendor/sweetalert dist/vendor/vanilla-fitvids dist/vendor/vue 

dist/vendor/baguettebox.js:
	mkdir dist/vendor/baguettebox.js
	cp -pR node_modules/baguettebox.js/dist dist/vendor/baguettebox.js/dist

dist/vendor/croppie:
	cp -pR node_modules/croppie             dist/vendor

dist/vendor/masonry-layout:
	cp -pR node_modules/masonry-layout      dist/vendor

dist/vendor/mediaelement:
	mkdir dist/vendor/mediaelement
	cp -pR node_modules/mediaelement/build  dist/vendor/mediaelement/build

dist/vendor/moment:
	mkdir dist/vendor/moment
	cp -pR node_modules/moment/dist         dist/vendor/moment/dist
	cp -pR node_modules/moment/min          dist/vendor/moment/min

dist/vendor/pikaday:
	cp -pR node_modules/pikaday             dist/vendor

dist/vendor/pikaday-time:
	cp -pR node_modules/pikaday-time        dist/vendor

dist/vendor/sweetalert:
	mkdir dist/vendor/sweetalert
	cp -pR node_modules/sweetalert/dist     dist/vendor/sweetalert/dist

dist/vendor/vanilla-fitvids:
	mkdir dist/vendor/vanilla-fitvids
	cp -pR node_modules/vanilla-fitvids/.     dist/vendor/vanilla-fitvids

dist/vendor/vue:
	mkdir dist/vendor/vue
	cp -pR node_modules/vue/dist            dist/vendor/vue/dist


#.PHONY:
dist: dist_js dist_css

.PHONY:
dist_js: directories
	./node_modules/.bin/webpack

.PHONY:
dist_css:
	cp  css/*.css dist/css

	cp js/widget/ai-image-generation/ai-image-generation.css dist/widget/
	cp js/widget/blog_featured_image/blog_featured_image.css dist/widget/
	cp js/widget/inline-edit/inline-edit.css                 dist/widget/
	cp js/widget/list/list.css                               dist/widget/
	cp js/widget/manage_navigation/manage-navigation.css     dist/widget/

.PHONY:
clean:
	rm dist/*.js
	rm -rf dist/css/*.css
	rm -rf dist /vendor/*
	rm -f  dist/widget/*.css
	rm -f  dist/widget/*.js

