[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nfqakademija/illuminati/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/nfqakademija/illuminati/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/nfqakademija/illuminati/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/nfqakademija/illuminati/?branch=develop)
[![Build Status](https://scrutinizer-ci.com/g/nfqakademija/illuminati/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/nfqakademija/illuminati/build-status/develop)
#Team illuminati
2015 NFQ Academy autumn session

### How to start with frontend
1. **npm update** (install gulp and other dependencies)
2. **sudo bower update --allow-root** (downloads frontend packages like "bootstrap-sass")
3. **gulp** (generates combined files web/assets/all.js, web/assets/master.css)

### Changing .scss or javascripts
**gulp watch** (after this command you can do changes in your .scss or .js, and gulp will update it automatically)  

### More info
**bower.json** - for frontend packages  
**gulpfile.js** - add your bundle JavaScripts files  
**./app/Resources/public/sass/master.scss** - for including your bundle styles (.scss) or writing common styles  
