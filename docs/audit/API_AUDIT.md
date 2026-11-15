# API_AUDIT

Base /api/v1 (~17 routes): publik register/login, properties(+slug), projects, articles, leads, ideas(+features); auth me/logout/favorites/orders. Error JSON dipaksa untuk api/*. Throttle: grup api default Laravel (verifikasi via route:list -v) + 10/mnt untuk auth. Token abilities didefinisikan tapi tidak di-enforce. Docs Scramble /docs/api + /docs/api.json. Kontrak Flutter: docs/API.md. Unpublished: show kini 404 (disamakan web).
