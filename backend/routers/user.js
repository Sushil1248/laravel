const User = require('../controller/user')
const route = require('express').Router()

route.post('/add',User.saveData)
route.get('/verify-email/:hash',User.verifyEmailLink);
route.post('/socket',User.socketConnection)
route.post('/notif/1',User.notif)
route.get('/findLatLong/:id',User.findLatLong)
route.post('/userLogs',User.userData)
route.get('/fetchlatestcoordinates/:id',User.fetchLatestCoordinates)
route.get('/travelLogs/',User.travelLogs)




module.exports=route;