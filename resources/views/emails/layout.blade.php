<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:white;border-radius:10px;overflow:hidden;box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                <!-- HEADER -->
<tr>
    <td style="background:#0ea5e9;padding:20px;text-align:center;color:white;">

        @if(!empty($company->logo ?? null))
            <img src="{{ asset('storage/' . $company->logo) }}" 
                 style="max-height:60px;margin-bottom:10px;">
        @endif

        <h2 style="margin:0;">
            {{ $company->name ?? 'Company' }}
        </h2>

    </td>
</tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:25px;">
                        @yield('content')
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
