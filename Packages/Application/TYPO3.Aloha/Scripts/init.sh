cd ../Resources/Public/
git clone -b dev-blocks --recursive git://github.com/alohaeditor/Aloha-Editor.git Aloha-dev

cd ../../Scripts/
./update-to-currently-working-version.sh

echo "now, set "Aloha:useGitSubmodule" to 'true' in your Settings.yaml"
