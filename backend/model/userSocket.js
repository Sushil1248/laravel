const mongoose = require('mongoose')

const userSocket = mongoose.Schema({
  userId: { type: String },
  deviceId: { type: String },
  deviceToken: { type: String },
  deviceLatitude: { type: String },
  deviceLongitude: { type: String },
  // timestamp:{type:String},
  date:{type:String},
  time:{type:String}
});

module.exports = mongoose.model("UserSocket", userSocket)

//save user location data received from poonam