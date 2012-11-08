./update-to-currently-working-version.sh

cd ../Resources/Public/

rm -R Aloha
cp -R Aloha-dev Aloha
cd Aloha
find . | grep .git | xargs rm -Rf

rm -R vendor/
# TODO: include extra/linkbrowser again!
rm -R build/ doc/ src/test src/plugins/extra src/plugins/media src/plugins/collaboration src/demo
rm -R src/lib/vendor/ext-3.2.1/resources/images/yourtheme
rm -R src/lib/vendor/ext-3.2.1/pkgs

rm -R src/lib/vendor/ext-3.2.1/resources/css/theme-*
rm -R src/lib/vendor/ext-3.2.1/resources/css/structure
rm -R src/lib/vendor/ext-3.2.1/resources/css/visual
rm -R src/lib/vendor/ext-3.2.1/resources/images/access
rm -R src/lib/vendor/ext-3.2.1/resources/images/vista