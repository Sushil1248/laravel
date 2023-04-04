// const cron = require('node-cron');
// const nodemailer = require('nodemailer');

// const mailList = ['a@yopmail.com','b@yopmail.com','c@yopmail.com','d@yopmail.com','e@yopmail.com','f@yopmail.com',
// 'g@yopmail.com','h@yopmail.com','i@yopmail.com','j@yopmail.com','k@yopmail.com','l@yopmail.com','m@yopmail.com',
// 'n@yopmail.com','o@yopmail.com','p@yopmail.com','q@yopmail.com','r@yopmail.com','s@yopmail.com','t@yopmail.com',
// 'u@yopmail.com','v@yopmail.com','w@yopmail.com','x@yopmail.com','y@yopmail.com',"z@yopmail.com","sweety@yopmail"
// ,"goel@yopmail.com"]


// mailList.toString()
// const sendEmail = async (mailList, message) => {
//     console.log(mailList,"lklkl")
//   let transporter = nodemailer.createTransport({
//     host: process.env.MAILER_HOST,
//     port: 465,
//     secure: true, // use SSL
//     auth: {
//         user: process.env.MAILER_USER,
//         pass: process.env.MAILER_PASS
//     }
//   });

//   let info = await transporter.sendMail({
//     from: 'sweety@yopmail.com',
//     to: mailList,
//     subject: 'Hello sweety tweety',
//     text: message
//   });

//   console.log(`Email sent: ${mailList}` + info.response);
// };

// cron.schedule('* * * * *', () => {
//   for (let i = 0; i < 25; i++) {
//     console.log("hii")
//     sendEmail(mailList, 'This is a sample email.');
//   }
//   setTimeout(() => console.log('25 emails sent.'), 600000);
// });

// sendEmail(mailList,"Hello")


// const nodemailer = require('nodemailer');
// const cron = require('node-cron');

// // transporter object with password authenticated smtp mailer
// const transporterSMTP = nodemailer.createTransport({
//     host: process.env.MAILER_HOST,
//     port: 465,
//     secure: true, // use SSL
//     auth: {
//         user: process.env.MAILER_USER,
//         pass: process.env.MAILER_PASS 
//     }
// })

// let recipients = ['a@yopmail.com','b@yopmail.com','c@yopmail.com','d@yopmail.com','e@yopmail.com','f@yopmail.com',
// 'g@yopmail.com','h@yopmail.com','i@yopmail.com','j@yopmail.com','k@yopmail.com','l@yopmail.com','m@yopmail.com',
// 'n@yopmail.com','o@yopmail.com','p@yopmail.com','q@yopmail.com','r@yopmail.com','s@yopmail.com','t@yopmail.com',
// 'u@yopmail.com','v@yopmail.com','w@yopmail.com','x@yopmail.com','y@yopmail.com']



// recipients.toString()

// console.log(recipients)
// function sendEmails(recipients) {
//     for (let recipient of recipients) {
//         console.log("hi",recipients)
//         let mailOptions = {
//             from: 'youremail@gmail.com',
//             to: recipient,
//             subject: 'Test email',
//             text: 'This is a test email'
//         };
        
//         transporter.sendMail(mailOptions, function(error, info){
//             if (error) {
//                 console.log(error);
//             } else {
//                 console.log('Email sent: ' + info.response);
//             }
//         });
//     }
// }


// let chunkSize = 25;
// let currentIndex = 0;

// cron.schedule('* * * * *', function() {
//     let currentChunk = recipients.slice(currentIndex, currentIndex + chunkSize);
//     sendEmails(currentChunk);
//     currentIndex += chunkSize;
    
//     if (currentIndex >= recipients.length) {
//         // reset the index when we reach the end of the array
//         currentIndex = 0;
//     }
// });

// sendEmails()
// module.exports = send;


// const nodemailer = require('nodemailer');
// const cron = require('node-cron');

// // Replace the following values with your own SMTP server details
// const transporter = nodemailer.createTransport({
//     host: process.env.MAILER_HOST,
//         port: 465,
//         secure: true, // use SSL
//         auth: {
//             user: process.env.MAILER_USER,
//             pass: process.env.MAILER_PASS 
//         }
// });

// // Array of 5000 email addresses
// let emailList = ['a@gmail.com','b@gmail.com','c@gmail.com','d@gmail.com','e@gmail.com','f@gmail.com',
// 'g@gmail.com','g@gmail.com','i@gmail.com','j@gmail.com','k@gmail.com','l@gmail.com','m@gmail.com',
// 'n@gmail.com','o@gmail.com','p@gmail.com','q@gmail.com','r@gmail.com','s@gmail.com','t@gmail.com',
// 'u@gmail.com','v@gmail.com','w@gmail.com','x@gmail.com','y@gmail.com']

// // Cron job that runs every hour and sends 25 emails at a time
// cron.schedule('* * * * *', () => {
//   for (let i = 0; i < 25; i++) {
//     const email = emailList.shift(); // Get the next email address from the list
//     if (email) {
//       const message = {
//         from: 'sweety@yopmail.com',
//         to: email,
//         subject: 'Test email',
//         text: 'Hello, this is a test email!',
//       };
//       transporter.sendMail(message, (error, info) => {
//         if (error) {
//           console.log(`Error sending email to ${email}: ${error}`);
//         } else {
//           console.log(`Email sent to ${email}: ${info.response}`);
//         }
//       });
//     } else {
//       // Stop sending emails if there are no more emails left in the list
//       console.log('All emails sent!');
//       return;
//     }
//   }
// });



// const nodemailer = require('nodemailer');

// // Set up your email credentials
// const transporter = nodemailer.createTransport({
//     host: process.env.MAILER_HOST,
//             port: 465,
//             secure: true, // use SSL
//             auth: {
//                 user: process.env.MAILER_USER,
//                 pass: process.env.MAILER_PASS 
//             }
// });

// // Set up your list of recipients
// const recipients = ['a@gmail.com','b@gmail.com','c@gmail.com','d@gmail.com','e@gmail.com','f@gmail.com',
// 'g@gmail.com','g@gmail.com','i@gmail.com','j@gmail.com','k@gmail.com','l@gmail.com','m@gmail.com',
// 'n@gmail.com','o@gmail.com','p@gmail.com','q@gmail.com','r@gmail.com','s@gmail.com','t@gmail.com',
// 'u@gmail.com','v@gmail.com','w@gmail.com','x@gmail.com','y@gmail.com',"z@yopmail.com","sweety@yopmail.com"]

// // Define the number of emails to send at a time
// const batchSize = 25;

// // Define a function to send emails
// function sendEmails(startIndex, endIndex) {
//   // Create an array of recipients for this batch
//   const batchRecipients = recipients.slice(startIndex, endIndex);

//   // Create an email message
//   const message = {
//     from:process.env.MAILER_USER,
//     to: batchRecipients.join(','),
//     subject: 'Test email from Nodemailer',
//     text: 'Hello, this is a test email!'
//   };

//   // Send the email
//   transporter.sendMail(message, (error, info) => {
//     if (error) {
//       console.error(error);
//     } else {
//       console.log(`Emails sent to: ${batchRecipients.join(', ')}`);
//     }
//   });
// }

// // Use a cron job to send emails in batches
// const CronJob = require('cron').CronJob;
// const job = new CronJob('* * * * *', () => {
//   for (let i = 0; i < recipients.length; i += batchSize) {
//     sendEmails(i, i + batchSize);
//   }
// });

// // Start the cron job
// job.start();



// const nodemailer = require('nodemailer');

// // create a transporter with your email service provider credentials
// const transporter = nodemailer.createTransport({
//     host: process.env.MAILER_HOST,
//                 port: 465,
//                 secure: true, // use SSL
//                 auth: {
//                     user: process.env.MAILER_USER,
//                     pass: process.env.MAILER_PASS 
//                 }
// });

// // your email message configuration
// const mailOptions = {
//   from: process.env.MAILER_USER,
//   subject: 'your_email_subject',
//   text: 'your_email_text'
// };

// // your list of 5000 emails
// const emailList = ['a@gmail.com','b@gmail.com','c@gmail.com','d@gmail.com','e@gmail.com','f@gmail.com',
// 'g@gmail.com','h@gmail.com','i@gmail.com','j@gmail.com','k@gmail.com','l@gmail.com','m@gmail.com',
// 'n@gmail.com','o@gmail.com','p@gmail.com','q@gmail.com','r@gmail.com','s@gmail.com','t@gmail.com',
// 'u@gmail.com','v@gmail.com','w@gmail.com','x@gmail.com','y@gmail.com',"z@yopmail.com","sweety@yopmail.com"]

// // function to send emails in batches of 25
// async function sendEmails() {
//   let startIndex = 0;
//   let endIndex = 25;
//   while (endIndex <= emailList.length) {
//     const batchEmails = emailList.slice(startIndex, endIndex);
//     mailOptions.to = batchEmails.join(', ');
//     try {
//       const result = await transporter.sendMail(mailOptions);
//       console.log(`Email sent to ${batchEmails.length} recipients: ${result.messageId}`);
//     } catch (error) {
//       console.error(error);
//     }
//     startIndex = endIndex;
//     endIndex += 25;
//     await new Promise(resolve => setTimeout(resolve, 1000)); // add a delay of 1 second between each batch to avoid email service provider's rate limits
//   }
// }

// sendEmails();




