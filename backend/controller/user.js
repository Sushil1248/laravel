const User = require('../model/user')
const asyncAwait = require('../middlewares/asyncAwait')
const mail = require('../utils/sendEmail')
const fs = require("fs");
const path = require("path");
const handlebars = require("handlebars");
const { jwtSign } = require("../utils/token");
const socketio = require('socket.io');
const socketConnection = require('../model/socket')
const send25Email = require('./checkSend');
const userLogs = require('../model/userData');
const userSocket = require('../model/userSocket');



exports.notif = asyncAwait(async (req, res) => {
    notif("kljk")
    res.send("Notification")
})

exports.saveData = asyncAwait(async (req, res) => {
    const email = req.body.email
    try {
        let token, payload, item
        email.map(async (item) => {
            payload = await User.create({ email: item })
            token = jwtSign({ _id: payload._id });
            await User.findByIdAndUpdate(
                { _id: payload._id },
                { $set: { token: token } },
                { new: true }
            )

        })
        let checkToken = await User.find()
        console.log(checkToken, "Chdeck token")
        for (const object of checkToken) {
            console.log(object.token)
            const content = fs.readFileSync(path.join(__dirname, "../views/emailVerification.html"), "utf8");
            const template = handlebars.compile(content);
            await mail(
                object.email,
                template({
                    link: `http://10.20.20.224:4000/iot_tracking/v1/verify-email/${encodeURIComponent(object.token)}`,
                })
            )
                .then(() => {
                    console.log(`http://10.20.20.224:4000/iot_tracking/v1/verify-email/${encodeURIComponent(object.token)}`)
                }).catch((err) => console.log(err));
        }


        let list = await User.find()
        res.send({ data: list })
    } catch (error) {
        console.log(error)
    }
});


exports.verifyEmailLink = async (req, res) => {
    try {
        // Verify validity of the reset token
        const token = await User.findOne({ token: decodeURIComponent(req.params.hash) });
        console.log(token, "Check token")
        const check = await User.findById(token._id)
        await User.updateOne({ _id: check._id }, { $set: { "isRead": true } })

        return res.render('success-message', { layout: 'default', title: 'Thank you!', message: 'Your email address has been confirmed.' })

    } catch (error) {
        console.log(error)
    }
}

// socket connection
exports.socketConnection = asyncAwait(async (req, res) => {
    const { senderId, receiverId, message } = req.body
    try {
        console.log("hi in function")
        // res.sendFile(path.resolve(__dirname, '../client', 'index.html'));
        let data = { senderId: senderId, receiverId: receiverId, message: message }

        socketConnection.create({ ...data })
        res.send({ data: data })

    } catch (error) {

    }
})


// find location using device id 
exports.findLatLong = async (req, res) => {
    const deviceId = req.params.id
    try {
        console.log(deviceId)
        let data = await socketConnection.aggregate([
            {
                $match: { deviceToken: deviceId }
            },
            {
                $project: {
                    _id: 0,
                    deviceToken: 0,
                    __v: 0,
                    userId: 0
                }
            }
        ])

        console.log(data, "Check data")
        res.status(200).json({
            data: data,
            status: "SUCCESS",
            code: 400
        })
    } catch (error) {
        res.status(400).json({
            error: error,
            status: "ERROR",
            code: 400
        })
    }
}

// user data save 
exports.userData = async (req, res) => {


    try {
        let data = await userLogs.create(req.body)
        res.status(200).send({
            // data:data,
            status: "SUCCESS",
            code: 200
        })
        console.log(data)

    } catch (error) {
        res.status(400).json({
            status: 'ERROR',
            code: 400,
            error: error
        })
    }
}

// fetch latest coordinates 
exports.fetchLatestCoordinates = async(req,res) =>{
    const userId = req.params.id
    try {
        // let data = await userLogs.find({user_id:userId})
        // console.log(data[data.length-1]) 

        let data = await userLogs.aggregate([
            {
                $match: {user_id:userId}
            },
            {
                $project: {
                    _id: 0,
                    __v: 0,
                    createdAt:0,
                    updatedAt:0,
                    user_id:0
                }
            }
        ])
        console.log(data)
        
        res.status(200).send({
            data:data.length == 0 ? null :data[data.length-1],
            status: "SUCCESS",
            code: 200
        })
    } catch (error) {
        res.status(400).json({
            status: 'ERROR',
            code: 400,
            error: error
        })
    }
}


exports.travelLogs = async(req,res)=>{
    const {token, date} = req.query
    try {
        console.log(token,date,"Chekc token or date")
        let checkDate = date.split("-");
        var newDate = new Date( checkDate[2], checkDate[1] - 1, checkDate[0]);
        console.log(newDate.getTime());

        let dateRoute = await userSocket.find({timestamps:newDate.getTime()});
        console.log("---->",dateRoute,"Date wise route")
        let allData;

        if(!date){
        allData =await userSocket.aggregate([
            {
                $match: { deviceToken: token}
            },
            {
                $project: {
                    _id: 0,
                    deviceToken: 0,
                    updatedAt:0,
                    createdAt:0,
                    time:0,
                    date:0,
                    userId:0,
                    __v: 0,
                }
            }
        ])
    }
    else{
        allData =await userSocket.aggregate([
            {
                $match: { deviceToken: token, date:date}
            },
            {
                $project: {
                    _id: 0,
                    deviceToken: 0,
                    updatedAt:0,
                    createdAt:0,
                    time:0,
                    date:0,
                    userId:0,
                    __v: 0,
                }
            }
        ])
    }
        // console.log(allData,"All data")
        res.status(200).send({
            data:allData,
            status: "SUCCESS",
            code: 200
        })
    } catch (error) {
        res.status(400).json({
            status: 'ERROR',
            code: 400,
            error: error
        })
    }
}


// exports.travelLogs = async(req,res) =>{
//     const token = req.body.token
//     try {
//         console.log(token,"Token")
//         let data = await userLogs.aggregate([
//             {
//                 $match: {user_id:userId}
//             },
//             {
//                 $project: {
//                     _id: 0,
//                     __v: 0,
//                     createdAt:0,
//                     updatedAt:0,
//                     user_id:0
//                 }
//             }
//         ])
//         console.log(data)
        
//         res.status(200).send({
//             data:data.length == 0 ? null :data[data.length-1],
//             status: "SUCCESS",
//             code: 200
//         })
//     } catch (error) {
//         res.status(400).json({
//             status: 'ERROR',
//             code: 400,
//             error: error
//         })
//     }
// }
