cd ../Resources/Private/PHP
rm -Rf PhpProfiler
git clone https://github.com/sandstorm/PhpProfiler.git
rm -R PhpProfiler/.git
git add PhpProfiler
git commit -m "Updated PHP Profiler"