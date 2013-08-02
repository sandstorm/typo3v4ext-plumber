Plumber connector for TYPO3 CMS
-------------------------------

You can also profile TYPO3 CMS using Plumber[1]. For that, you need to install
https://github.com/sandstorm/typo3v4ext-plumber like this:

```bash
cd typo3conf/ext; git clone https://github.com/sandstorm/typo3v4ext-plumber sandstormmedia_plumber
```

Furthermore, you need a running TYPO3 Flow installation which is used to show the
profiling data with Plumber.

After installing the extension in TYPO3 CMS, you need to specify the base path
to the Flow installation inside the extension configuration.

Then, flush your caches and you should see a profiling run appear in Plumber
for every page request in TYPO3 CMS.

[1] https://github.com/sandstorm/Plumber
