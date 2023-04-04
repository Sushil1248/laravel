const mongoose = require('mongoose')

const schema = mongoose.Schema({
    email:{type:Array},
    isRead:{type:String,default:false},
    token:{type:String,default:""}
})

module.exports = mongoose.model("User",schema)