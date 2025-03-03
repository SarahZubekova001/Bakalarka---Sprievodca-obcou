# 1) Základný Node image
FROM node:18-alpine AS build

# 2) Nastav pracovný adresár v kontajneri
WORKDIR /usr/src/app

# 3) Skopíruj package.json a package-lock.json (ak ho máš)
COPY package*.json ./

# 4) Nainštaluj závislosti
RUN npm install

# 5) Skopíruj zvyšok zdrojových kódov
COPY . .

# 6) Štandardne, ak by sme chceli production build:
# RUN npm run build
# a potom serve - nepotrebuješ dev server

# V tomto príklade ukážeme priamo spustenie dev servera Svelte/Vite:
    EXPOSE 8080
    CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "8080"]


    