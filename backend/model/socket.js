const mongoose = require('mongoose')

const socketSchema = mongoose.Schema({
    deviceToken: {type:String},
    deviceLatitude: {type:String},
    deviceLongitude: {type:String},
    userId: {type:Number}
})

module.exports = mongoose.model("socketSchema",socketSchema)