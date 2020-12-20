Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'orderable',
      path: '/orderable',
      component: require('./components/Tool'),
    },
  ])
})
