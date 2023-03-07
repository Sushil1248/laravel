<?php

return [
//Error message
    'ERROR' => [
        'OOPS_ERROR'        => 'Oops!! Something went wrong or your session has been expired',
        'FORBIDDEN_ERROR'   => 'Oops!! Something went wrong.',
        'TOKEN_INVALID'     => 'Oops, the link has been expired',
        'WRONG_CREDENTIAL'  => 'Please provide valid credentials.',
        'ACCOUNT_ISSUE'     => 'Oops! your account is not active yet. Please verify your email or contact administrator.',
        'IMAGE_TYPE'        => 'Please select png or jpg type image.',
        'PASSWORD_MISMATCH' => 'Current Password Does not Match',
        'PASSWORD_SAME'     => 'New Password cannot be same as your current password',
        'INVALID_COUPON'    => 'Invalid / Expired coupon',
        'DELETE_ERROR'      => 'You can not delete this question as it is associate with one of the answer(s)',
        'NOT_ELIGIBLE'      => 'Sorry You are not eligible to apply coupon!! Try to apply on second purchase',
        'LOGIN_DISCOUNT'    => 'Sorry!! Logged in to your account to avail discount on this purchase.',
    ],
    'SUCCESS' => [
        'ACCEPT_DONE' => 'has been accepted successfully.',
        'REJECT_DONE' => 'has been rejected successfully.',
        'ASSIGN_DONE' => 'has been assigned successfully.',
        'UPDATE_DONE' => 'has been updated successfully.',
        'CART_UPDATE' => 'Cart has been updated successfully with case.',
        'CREATE_DONE' => 'has been created successfully.',
        'COPY_DONE' => 'has been copied successfully.',
        'SUBMIT_DONE' => 'has been submitted successfully.',
        'DELETE_DONE' => 'has been deleted successfully.',
        'RESTORE_DONE' => 'has been restored successfully.',
        'STATUS_UPDATE' => 'status has been updated successfully.',
        'REPLY_SENT' => 'Reply has been sent successfully.',
        'RESET_LINK_MAIL' => 'We have sent you an email with password reset link.',
        'CONTACT_DONE' => 'Your message has been sent successfully. Waiting for administrator reply',
        'WELCOME' => 'Thank you for verifing you email.',
        'WELCOME_LOGIN' => 'Thank you for verifing you email. Login to your account.',
        'ACCOUNT_CREATED' => 'Welcome to SwimFun, your account has been created successfully.',
        'COUPON_APPLIED' => 'Coupon has been applied successfully',
        'PURCHASE_DONE' => 'Thanks to purchase case, your purchase is successfully submitted',
        'AVAIL_DISCOUNT' => 'You are eligible to avail discount. Apply discount coupon if you have any.',
        'THANKS_FOR_CONTACT' => 'Thanks for contacting us, we will contact you soon',
        'APPROVED_DONE' => 'has been approved successfully.',
        'DECLINED_DONE' => 'has been declined successfully.',
        'CANCELLED_DONE' => 'has been cancelled successfully.',
        'DELEVERED_DONE' => 'has been delevered successfully.',
    ],
    'PAGINATION_NUMBER' => '20',
    'API_PAGINATION_NUMBER'  =>  '20',
    'ADMIN_EMAIL' => 'gurpreet.maan@shinedezign.com',
    'WEEK_DAYS'     =>  array(
        1   => "Sunday",
        2   => "Monday",
        3   => "Tuesday",
        4   =>  "Wednesday",
        5   =>  "Thursday",
        6   =>  "Friday",
        7   =>  "Saturday"
    ),
    'MAXIMUM_UPLOAD'    =>  2,
    'EXERCISE_TYPE'     =>  [
        'straight_sets' =>  'Perform the same weight each set',
        'ascending_pyramid' =>  'Increase the weight each set',
        'descending_pyramid'    =>  'Decrease the weight set',
        'superset'  =>  'Alternate these 2 exercises after each set',
        'triset'    =>  'Alternate these 3 exercises after each set',
        'giant_set' =>  'Alternate these 4 exercises after each set',
        'dropset'   =>  'Drop the weight each set without any rest',
        'rest_pause'    =>  'Perform 1 set as 3 mini sets with short breaks',
        'backdown_sets'  =>  'More volume at a reduced weight'
    ],
    'FORUM_USER_TYPE' => [
        'all' => 'All User',
        'male' => 'Male Only',
        'female' => 'Female Only'
    ],
    'API_TESTING'   =>  1,
    'NOTIFICATION_MESSAGE'  =>  [
        'answered_forum'    =>  "Answered your forum Question",
        'replied_answer' =>  "Replied on your Answer",
        'liked_answer'  =>  "Liked your Answer"
    ]
];