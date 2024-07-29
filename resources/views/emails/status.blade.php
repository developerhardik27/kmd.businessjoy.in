@php
    $status = ucwords(str_replace('_', ' ', $reminder->status));
@endphp

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta content="width=device-width" name="viewport" />
    <!--[if !mso]><!-->
    <meta content="IE=edge" http-equiv="X-UA-Compatible" />
    <!--<![endif]-->
    <title></title>
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{ asset('admin/css/typography.css') }}">
    <!--<![endif]-->
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: 'Muli', sans-serif;
        }

        table,
        td,
        tr {
            vertical-align: top;
            border-collapse: collapse;
        }

        * {
            line-height: inherit;
        }

        a[x-apple-data-detectors=true] {
            color: inherit !important;
            text-decoration: none !important;
        }
    </style>
    <style id="media-query" type="text/css">
        @media (max-width: 700px) {

            .block-grid,
            .col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important;
            }

            .block-grid {
                width: 100% !important;
            }

            .col {
                width: 100% !important;
            }

            .col_cont {
                margin: 0 auto;
            }

            img.fullwidth,
            img.fullwidthOnMobile {
                width: 100% !important;
            }

            .no-stack .col {
                min-width: 0 !important;
                display: table-cell !important;
            }

            .no-stack.two-up .col {
                width: 50% !important;
            }

            .no-stack .col.num2 {
                width: 16.6% !important;
            }

            .no-stack .col.num3 {
                width: 25% !important;
            }

            .no-stack .col.num4 {
                width: 33% !important;
            }

            .no-stack .col.num5 {
                width: 41.6% !important;
            }

            .no-stack .col.num6 {
                width: 50% !important;
            }

            .no-stack .col.num7 {
                width: 58.3% !important;
            }

            .no-stack .col.num8 {
                width: 66.6% !important;
            }

            .no-stack .col.num9 {
                width: 75% !important;
            }

            .no-stack .col.num10 {
                width: 83.3% !important;
            }

            .video-block {
                max-width: none !important;
            }

            .mobile_hide {
                min-height: 0px;
                max-height: 0px;
                max-width: 0px;
                display: none;
                overflow: hidden;
                font-size: 0px;
            }

            .desktop_hide {
                display: block !important;
                max-height: none !important;
            }

            .img-container.big img {
                width: auto !important;
            }
        }
    </style>
    <style id="menu-media-query" type="text/css">
        @media (max-width: 700px) {
            .menu-checkbox[type="checkbox"]~.menu-links {
                display: none !important;
                padding: 5px 0;
            }

            .menu-checkbox[type="checkbox"]~.menu-links span.sep {
                display: none;
            }

            .menu-checkbox[type="checkbox"]:checked~.menu-links,
            .menu-checkbox[type="checkbox"]~.menu-trigger {
                display: block !important;
                max-width: none !important;
                max-height: none !important;
                font-size: inherit !important;
            }

            .menu-checkbox[type="checkbox"]~.menu-links>a,
            .menu-checkbox[type="checkbox"]~.menu-links>span.label {
                display: block !important;
                text-align: center;
            }

            .menu-checkbox[type="checkbox"]:checked~.menu-trigger .menu-close {
                display: block !important;
            }

            .menu-checkbox[type="checkbox"]:checked~.menu-trigger .menu-open {
                display: none !important;
            }

            #menuqhqde8~div label {
                border-radius: 0% !important;
            }

            #menuqhqde8:checked~.menu-links {
                background-color: #ffffff !important;
            }

            #menuqhqde8:checked~.menu-links a {
                color: #292f37 !important;
            }

            #menuqhqde8:checked~.menu-links span {
                color: #292f37 !important;
            }

            #menuo4j4kn~div label {
                border-radius: 0% !important;
            }

            #menuo4j4kn:checked~.menu-links {
                background-color: #ffffff !important;
            }

            #menuo4j4kn:checked~.menu-links a {
                color: #292f37 !important;
            }

            #menuo4j4kn:checked~.menu-links span {
                color: #292f37 !important;
            }

            #menu12g67q~div label {
                border-radius: 0% !important;
            }

            #menu12g67q:checked~.menu-links {
                background-color: #000000 !important;
            }

            #menu12g67q:checked~.menu-links a {
                color: #ffffff !important;
            }

            #menu12g67q:checked~.menu-links span {
                color: #ffffff !important;
            }
        }
    </style>
</head>

<body class="clean-body"
    style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background: linear-gradient(to right, #97271f, #253566);">
    <table bgcolor="#e1cfc3" cellpadding="0" cellspacing="0" class="nl-container" role="presentation"
        style="margin:70px 0; table-layout: fixed; vertical-align: top; min-width: 320px; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #e1cfc3; width: 100%;"
        valign="top" width="100%">
        <tbody>
            <tr style="vertical-align: top;" valign="top">
                <td style="word-break: break-word; vertical-align: top;" valign="top">
                    <div style="background: linear-gradient(to right, #97271f, #253566);">
                        <div class="block-grid"
                            style="min-width: 320px; max-width: 680px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: #ffffff;">
                            <div style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                                <div class="col num12"
                                    style="min-width: 320px; max-width: 680px; display: table-cell; vertical-align: top; width: 680px;">
                                    <div class="col_cont" style="width:100% !important;">
                                        <div
                                            style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                                            <table border="0" cellpadding="0" cellspacing="0" class="divider"
                                                role="presentation"
                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                valign="top" width="100%">
                                                <tbody>
                                                    <tr style="vertical-align: top;" valign="top">
                                                        <td class="divider_inner"
                                                            style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px;"
                                                            valign="top">
                                                            <table align="center" border="0" cellpadding="0"
                                                                cellspacing="0" class="divider_content" height="15"
                                                                role="presentation"
                                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; height: 15px; width: 100%;"
                                                                valign="top" width="100%">
                                                                <tbody>
                                                                    <tr style="vertical-align: top;" valign="top">
                                                                        <td height="15"
                                                                            style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                            valign="top">
                                                                            <span></span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div align="center" class="img-container center fixedwidth"
                                                style="padding-right: 0px;padding-left: 0px;">
                                                <div style="font-size:1px;line-height:5px">
                                                </div>
                                                <img align="center" alt="BusinessJoy-logo" border="0"
                                                    class="center fixedwidth"
                                                    src="{{ asset('admin/images/bjlogo2.png') }}"
                                                    style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; width: 136px; max-width: 100%; display: block;"
                                                    title="BuinessJoy-logo" width="136" />
                                                <div style="font-size:1px;line-height:20px">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: linear-gradient(to right, #97271f, #253566);">
                        <div class="block-grid"
                            style="min-width: 320px; max-width: 680px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: #ffffff;">
                            <div style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                                <div class="col num12"
                                    style="min-width: 320px; max-width: 680px; display: table-cell; vertical-align: top; width: 680px;">
                                    <div class="col_cont" style="width:100% !important;">
                                        <div
                                            style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
                                            <table cellpadding="0" cellspacing="0" role="presentation"
                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;"
                                                valign="top" width="100%">
                                                <tr style="vertical-align: top;" valign="top">
                                                    <td align="center"
                                                        style="word-break: break-word; vertical-align: top; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px; text-align: center; width: 100%;"
                                                        valign="top" width="100%">
                                                        <h1
                                                            style="color:#292f37;direction:ltr;font-size:32px;font-weight:normal;line-height:120%;text-align:center;margin-top:0;margin-bottom:0;">
                                                            Complain Status : {{ $status }}</h1>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div
                                                style="color:#292f37;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;letter-spacing:5px;line-height:1.2;padding-top:0px;padding-right:10px;padding-bottom:0px;padding-left:10px;">
                                                <div class="txtTinyMce-wrapper"
                                                    style="line-height: 1.2; font-size: 12px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #292f37; letter-spacing: 5px; mso-line-height-alt: 14px;">
                                                    <p
                                                        style="margin: 0; text-align: center; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; letter-spacing: 5px; margin-top: 0; margin-bottom: 0;">
                                                        <strong>
                                                            <span style="font-size: 46px;">

                                                            </span>
                                                        </strong>
                                                    </p>
                                                </div>
                                            </div>

                                            <div
                                                style="color:#393d47;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;line-height:1.5;padding-top:10px;padding-right:40px;padding-bottom:10px;padding-left:40px;">
                                                <div class="txtTinyMce-wrapper"
                                                    style="line-height: 1.5; font-size: 12px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #393d47; mso-line-height-alt: 18px;">
                                                    <p
                                                        style="margin: 0; line-height: 1.5; word-break: break-word; text-align: center; font-size: 14px; mso-line-height-alt: 21px; margin-top: 0; margin-bottom: 0;">
                                                        <span style="font-size: 14px;">
                                                            Hello {{ $reminder->first_name }}
                                                            {{ $reminder->last_name }},
                                                        </span><br />
                                                        <span style="font-size: 14px;">
                                                            We are writing to inform you about the status update of your
                                                            complain with the following details:
                                                        </span>
                                                        <br />
                                                        <span style="font-size: 14px;">
                                                            <strong> Request Ticket Number :</strong>
                                                            {{ $reminder->ticket }}
                                                        </span>
                                                        <br />
                                                        <span style="font-size: 14px;">
                                                            <strong> Module Name :</strong> {{ $reminder->module_name }}
                                                        </span>
                                                        <br />
                                                        <span style="font-size: 14px;">
                                                            <strong> Issue Type :</strong> {{ $reminder->issue_type }}
                                                        </span>
                                                        <br />
                                                        <span style="font-size: 14px;">
                                                            <strong> Current Status : </strong> {{ $status }}
                                                        </span>
                                                        <br />
                                                        <span style="font-size: 14px;">
                                                            @if ($reminder->status === 'pending')
                                                                Your complain is currently pending our review. We
                                                                will
                                                                start working on it shortly.
                                                            @elseif($reminder->status === 'in_progress')
                                                                Your complain is currently in progress. Our
                                                                technicians
                                                                are actively working on resolving the issue.
                                                            @elseif($reminder->status === 'resolved')
                                                                We are pleased to inform you that your complain has
                                                                been
                                                                resolved. The issue has been successfully addressed.
                                                            @elseif($reminder->status === 'cancelled')
                                                                Your ticket has been cancelled. If you
                                                                have any further questions or need assistance,
                                                                please don't hesitate to contact us.
                                                            @endif
                                                        </span>
                                                        <br />
                                                        <span style="font-size: 14px;">
                                                            If you have any query or concerns, feel free to reply to
                                                            this email or contact our support team at
                                                            support@businessjoy.com
                                                        </span>
                                                        <br />
                                                        <span style="font-size: 14px;">
                                                            Thank you for choosing our support services.
                                                        </span>

                                                </div>
                                            </div>
                                            <table border="0" cellpadding="0" cellspacing="0" class="divider"
                                                role="presentation"
                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                valign="top" width="100%">
                                                <tbody>
                                                    <tr style="vertical-align: top;" valign="top">
                                                        <td class="divider_inner"
                                                            style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px;"
                                                            valign="top">
                                                            <table align="center" border="0" cellpadding="0"
                                                                cellspacing="0" class="divider_content"
                                                                height="10" role="presentation"
                                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; height: 10px; width: 100%;"
                                                                valign="top" width="100%">
                                                                <tbody>
                                                                    <tr style="vertical-align: top;" valign="top">
                                                                        <td height="10"
                                                                            style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                            valign="top"><span></span></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div
                                                style="color:#292f37;font-family:Georgia, Times, 'Times New Roman', serif;line-height:1.5;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;">
                                                <div class="txtTinyMce-wrapper"
                                                    style="line-height: 1.5; font-size: 12px;  color: #292f37; mso-line-height-alt: 18px;">
                                                    <p
                                                        style="margin: 0; text-align: center; line-height: 1.5; word-break: break-word; font-size: 20px; mso-line-height-alt: 30px; margin-top: 0; margin-bottom: 0;">
                                                        <span style="font-size: 15px;">
                                                            {{-- <strong>
                                                                Your Email :
                                                            </strong>
                                                            <span>mayur@email.com</span> --}}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div align="center" class="button-container"
                                                style="padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;">
                                                <a href="{{ route('admin.techsupport') }}"
                                                    style="-webkit-text-size-adjust: none; text-decoration: none; display: inline-block; color: #ffffff; background: linear-gradient(to right, #97271f, #253566); border-radius: 5px; -webkit-border-radius: 5px; -moz-border-radius: 5px; width: auto; width: auto; padding-top: 5px; padding-bottom: 5px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: center; mso-border-alt: none; word-break: keep-all;"
                                                    target="_blank"><span
                                                        style="padding-left:20px;padding-right:20px;font-size:16px;display:inline-block;letter-spacing:2px;"><span
                                                            style="font-size: 16px; line-height: 2; word-break: break-word; mso-line-height-alt: 32px;">
                                                            <strong style="color: #ffffff">
                                                                Go to Your Ticket
                                                            </strong></span></span></a>
                                            </div>
                                            <div style="background: linear-gradient(to right, #97271f, #253566);">
                                                <div class="block-grid"
                                                    style="min-width: 320px; max-width: 680px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: #ffffff;">
                                                    <div
                                                        style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                                                        <div class="col num12"
                                                            style="min-width: 320px; max-width: 680px; display: table-cell; vertical-align: top; width: 680px;">
                                                            <div class="col_cont" style="width:100% !important;">
                                                                <div
                                                                    style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                                                                    <table border="0" cellpadding="0"
                                                                        cellspacing="0" class="divider"
                                                                        role="presentation"
                                                                        style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                        valign="top" width="100%">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top;"
                                                                                valign="top">
                                                                                <td class="divider_inner"
                                                                                    style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px;"
                                                                                    valign="top">
                                                                                    <table align="center"
                                                                                        border="0" cellpadding="0"
                                                                                        cellspacing="0"
                                                                                        class="divider_content"
                                                                                        height="15"
                                                                                        role="presentation"
                                                                                        style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; height: 15px; width: 100%;"
                                                                                        valign="top" width="100%">
                                                                                        <tbody>
                                                                                            <tr style="vertical-align: top;"
                                                                                                valign="top">
                                                                                                <td height="15"
                                                                                                    style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                                                    valign="top">
                                                                                                    <span></span>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <table border="0" cellpadding="0"
                                                                        cellspacing="0" class="divider"
                                                                        role="presentation"
                                                                        style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                        valign="top" width="100%">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top;"
                                                                                valign="top">
                                                                                <td class="divider_inner"
                                                                                    style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 5px; padding-right: 15px; padding-bottom: 5px; padding-left: 15px;"
                                                                                    valign="top">
                                                                                    <table align="center"
                                                                                        border="0" cellpadding="0"
                                                                                        cellspacing="0"
                                                                                        class="divider_content"
                                                                                        role="presentation"
                                                                                        style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 1px solid #BBBBBB; width: 100%;"
                                                                                        valign="top" width="100%">
                                                                                        <tbody>
                                                                                            <tr style="vertical-align: top;"
                                                                                                valign="top">
                                                                                                <td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                                                    valign="top">
                                                                                                    <span></span>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="background: linear-gradient(to right, #97271f, #253566);">
                                                <div class="block-grid three-up"
                                                    style="min-width: 320px; max-width: 680px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; Margin: 0 auto; background-color: #ffffff;">
                                                    <div
                                                        style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                                                        <div class="col num4"
                                                            style="display: table-cell; vertical-align: top;float:left;width: 100%;">
                                                            <div class="col_cont" style="width:100% !important;">
                                                                <div
                                                                    style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:7px; padding-bottom:8px; padding-right: 0px; padding-left: 0px;">

                                                                    <div
                                                                        style="color:#292f37;line-height:1.2;padding-right:10px;padding-left:10px;">
                                                                        <div class="txtTinyMce-wrapper"
                                                                            style="line-height: 1.2; font-size: 14px; color: #292f37; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 14px;">
                                                                            <p
                                                                                style="margin: 0; font-size: 14px; line-height: 1.2; word-break: break-word; text-align: center; mso-line-height-alt: 14px; margin-top: 0; margin-bottom: 0;">
                                                                                <span style="font-size: 14px;">
                                                                                    © Copyright <a
                                                                                        href="https://businessjoy.in"
                                                                                        style="font-weight: 550"
                                                                                        target="_blank">Businessjoy</a>.
                                                                                    All Rights Reserved
                                                                                </span>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col num4"
                                                            style="display: table-cell; vertical-align: top;float:left; width: 100%;">
                                                            <div class="col_cont" style="width:100% !important;">
                                                                <div
                                                                    style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                                                                    <div
                                                                        style="color:#292f37;letter-spacing:1px;line-height:1.2;padding-right:10px;padding-left:20px;padding-bottom:12px;">
                                                                        <div class="txtTinyMce-wrapper"
                                                                            style="font-size: 14px; line-height: 1.2; color: #292f37;letter-spacing: 1px; mso-line-height-alt: 17px;">

                                                                            <p
                                                                                style="margin: 0; font-size: 14px; line-height: 1.2; word-break: break-word; text-align: center; mso-line-height-alt: 17px; letter-spacing: 1px; margin-top: 0; margin-bottom: 0;">
                                                                                <a href="{{route('privacypolicy')}}"
                                                                                    style="text-decoration: none">Privacy
                                                                                    & Policy</a> |
                                                                                <a href="{{route('termsandconditions')}}"
                                                                                    style="text-decoration: none">Terms
                                                                                    and conditions</a>|
                                                                                <a href="tel:+917948558535"
                                                                                    style="text-decoration: none">+91 7948558535</a> |
                                                                                <a href="mailto:support@businessjoy.com"
                                                                                    style="text-decoration: none">support@businessjoy.com</a>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
