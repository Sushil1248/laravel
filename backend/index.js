const express = require('express');
require("dotenv").config();
const notif = require('./utils/pushNotif')
const mongoose = require('./config')
const socketConnection = require('./app')


const app = express()
app.use(express.json());
app.use(express.urlencoded({extended: false}));
app.use(express.json());
app.set('view engine', 'hbs');
app.use('/iot_tracking/v1',require('./routers/user'))



app.listen(`${process.env.PORT}`,()=>{
    console.log(`server is running on port ${process.env.PORT}`)
})
