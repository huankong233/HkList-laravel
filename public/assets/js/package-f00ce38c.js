const e="94list-frontend",t="0.1.7",s={dev:"vite",build:'run-p type-check "build-only {@}" --',preview:"vite preview","build-only":"vite build","type-check":"vue-tsc --noEmit -p tsconfig.app.json --composite false",lint:"eslint . --ext .vue,.js,.jsx,.cjs,.mjs,.ts,.tsx,.cts,.mts --fix --ignore-path .gitignore",format:"prettier --write src/"},n={"@element-plus/icons-vue":"^2.1.0","@vueuse/core":"^10.5.0",axios:"^1.5.1",clipboard:"^2.0.11","element-plus":"^2.3.14",pinia:"^2.1.6",vue:"^3.3.4","vue-router":"^4.2.4"},i={"@rushstack/eslint-patch":"^1.3.3","@tsconfig/node18":"^18.2.2","@types/node":"^18.17.17","@vitejs/plugin-vue":"^4.3.4","@vue/eslint-config-prettier":"^8.0.0","@vue/eslint-config-typescript":"^12.0.0","@vue/tsconfig":"^0.4.0",eslint:"^8.49.0","eslint-plugin-vue":"^9.17.0","npm-run-all2":"^6.0.6",prettier:"^3.0.3",sass:"^1.69.0",typescript:"~5.2.0","unplugin-auto-import":"^0.16.6","unplugin-vue-components":"^0.25.2",vite:"^4.4.9","vue-tsc":"^1.8.11"},p={name:e,version:t,scripts:s,dependencies:n,devDependencies:i};export{p as default,n as dependencies,i as devDependencies,e as name,s as scripts,t as version};
