echo "switching to branch main"
git checkout main

echo "building app..."
npm run build

echo "deploying files to server..."
scp -r dist/* antoine@172.232.56.143:/var/www/172.232.56.143/

echo "Done!"

