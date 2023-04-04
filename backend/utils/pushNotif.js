// var FCM = require('fcm-push');
// require('dotenv').config()

// var serverKey = process.env.SERVER_KEY;
// var fcm = new FCM(serverKey);

// module.exports = async(data) =>{
// var message = {
//     to: `/topics/1`, // required fill with device token or topics
//     collapse_key: 'your_collapse_key',
//     data: {
//         your_custom_data_key: 'your_custom_data_value'
//     },
//     notification: {
//         title: 'Title of your push notification',
//         body: 'Body of your push notification'
//     }
// };



//promise style
// fcm.send(message)
//     .then(function(response){
//         console.log("Successfully sent with response: ", response);
//     })
//     .catch(function(err){
//         console.log("Something has gone wrong!");
//         console.error(err);
//     })
// }
