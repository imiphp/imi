<?php

declare(strict_types=1);

namespace Imi\Util\Http\Consts;

/**
 * 常见的媒体类型.
 */
class MediaType
{
    public const ALL = '*/*';

    public const APPLICATION_ATOM_XML = 'application/atom+xml';

    public const APPLICATION_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    public const APPLICATION_JSON = 'application/json';

    public const APPLICATION_JSON_UTF8 = 'application/json;charset=utf-8';

    public const APPLICATION_OCTET_STREAM = 'application/octet-stream';

    public const APPLICATION_PDF = 'application/pdf';

    public const APPLICATION_PROBLEM_JSON = 'application/problem+json';

    public const APPLICATION_PROBLEM_XML = 'application/problem+xml';

    public const APPLICATION_RSS_XML = 'application/rss+xml';

    public const APPLICATION_STREAM_JSON = 'application/stream+json';

    public const APPLICATION_XHTML_XML = 'application/xhtml+xml';

    public const APPLICATION_XML = 'application/xml';

    public const IMAGE_JPEG = 'image/jpeg';

    public const IMAGE_APNG = 'image/apng';

    public const IMAGE_PNG = 'image/png';

    public const IMAGE_GIF = 'image/gif';

    public const IMAGE_WEBP = 'image/webp';

    public const IMAGE_ICON = 'image/x-icon';

    public const MULTIPART_FORM_DATA = 'multipart/form-data';

    public const TEXT_EVENT_STREAM = 'text/event-stream';

    public const TEXT_HTML = 'text/html';

    public const TEXT_MARKDOWN = 'text/markdown';

    public const TEXT_PLAIN = 'text/plain';

    public const TEXT_XML = 'text/xml';

    public const GRPC = 'application/grpc';

    public const GRPC_PROTO = 'application/grpc+proto';

    public const GRPC_JSON = 'application/grpc+json';

    private static array $extMap = [
        'Extension' => 'Type/sub-type',
        '323'       => 'text/h323',
        'acx'       => 'application/internet-property-stream',
        'ai'        => 'application/postscript',
        'aiff'      => 'audio/x-aiff',
        'asf'       => 'video/x-ms-asf',
        'au'        => 'audio/basic',
        'avi'       => 'video/x-msvideo',
        'axs'       => 'application/olescript',
        'bcpio'     => 'application/x-bcpio',
        'bmp'       => 'image/bmp',
        'cat'       => 'application/vnd.ms-pkiseccat',
        'cdf'       => 'application/x-cdf',
        'clp'       => 'application/x-msclip',
        'cmx'       => 'image/x-cmx',
        'cod'       => 'image/cis-cod',
        'cpio'      => 'application/x-cpio',
        'crd'       => 'application/x-mscardfile',
        'crl'       => 'application/pkix-crl',
        'crt'       => 'application/x-x509-ca-cert',
        'csh'       => 'application/x-csh',
        'css'       => 'text/css',
        'dll'       => 'application/x-msdownload',
        'doc'       => 'application/msword',
        'docx'      => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dotx'      => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'xlsx'      => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'pptx'      => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'dvi'       => 'application/x-dvi',
        'dxr'       => 'application/x-director',
        'etx'       => 'text/x-setext',
        'evy'       => 'application/envoy',
        'fif'       => 'application/fractals',
        'gif'       => 'image/gif',
        'gtar'      => 'application/x-gtar',
        'gz'        => 'application/x-gzip',
        'hdf'       => 'application/x-hdf',
        'hlp'       => 'application/winhlp',
        'hqx'       => 'application/mac-binhex40',
        'hta'       => 'application/hta',
        'htc'       => 'text/x-component',
        'html'      => 'text/html',
        'htt'       => 'text/webviewhtml',
        'ico'       => 'image/x-icon',
        'ief'       => 'image/ief',
        'iii'       => 'application/x-iphone',
        'jfif'      => 'image/pipeg',
        'jpg'       => 'image/jpeg',
        'js'        => 'application/x-javascript',
        'latex'     => 'application/x-latex',
        'lsf'       => 'video/x-la-asf',
        'm3u'       => 'audio/x-mpegurl',
        'man'       => 'application/x-troff-man',
        'mdb'       => 'application/x-msaccess',
        'me'        => 'application/x-troff-me',
        'mhtml'     => 'message/rfc822',
        'mid'       => 'audio/mid',
        'mny'       => 'application/x-msmoney',
        'mov'       => 'video/quicktime',
        'movie'     => 'video/x-sgi-movie',
        'mpeg'      => 'video/mpeg',
        'mpp'       => 'application/vnd.ms-project',
        'ms'        => 'application/x-troff-ms',
        'mvb'       => 'application/x-msmediaview',
        'oda'       => 'application/oda',
        'p10'       => 'application/pkcs10',
        'p7m'       => 'application/x-pkcs7-mime',
        'p7r'       => 'application/x-pkcs7-certreqresp',
        'p7s'       => 'application/x-pkcs7-signature',
        'pbm'       => 'image/x-portable-bitmap',
        'pdf'       => 'application/pdf',
        'pfx'       => 'application/x-pkcs12',
        'pgm'       => 'image/x-portable-graymap',
        'pko'       => 'application/ynd.ms-pkipko',
        'pnm'       => 'image/x-portable-anymap',
        'ppm'       => 'image/x-portable-pixmap',
        'ppt'       => 'application/vnd.ms-powerpoint',
        'prf'       => 'application/pics-rules',
        'pub'       => 'application/x-mspublisher',
        'ram'       => 'audio/x-pn-realaudio',
        'ras'       => 'image/x-cmu-raster',
        'rgb'       => 'image/x-rgb',
        'rtf'       => 'application/rtf',
        'rtx'       => 'text/richtext',
        'scd'       => 'application/x-msschedule',
        'sct'       => 'text/scriptlet',
        'setpay'    => 'application/set-payment-initiation',
        'setreg'    => 'application/set-registration-initiation',
        'sh'        => 'application/x-sh',
        'shar'      => 'application/x-shar',
        'sit'       => 'application/x-stuffit',
        'spc'       => 'application/x-pkcs7-certificates',
        'spl'       => 'application/futuresplash',
        'src'       => 'application/x-wais-source',
        'sst'       => 'application/vnd.ms-pkicertstore',
        'stl'       => 'application/vnd.ms-pkistl',
        'svg'       => 'image/svg+xml',
        'sv4cpio'   => 'application/x-sv4cpio',
        'sv4crc'    => 'application/x-sv4crc',
        'swf'       => 'application/x-shockwave-flash',
        'tar'       => 'application/x-tar',
        'tcl'       => 'application/x-tcl',
        'tex'       => 'application/x-tex',
        'texi'      => 'application/x-texinfo',
        'tgz'       => 'application/x-compressed',
        'tiff'      => 'image/tiff',
        'trm'       => 'application/x-msterminal',
        'tsv'       => 'text/tab-separated-values',
        'txt'       => 'text/plain',
        'uls'       => 'text/iuls',
        'ustar'     => 'application/x-ustar',
        'vcf'       => 'text/x-vcard',
        'wav'       => 'audio/x-wav',
        'wmf'       => 'application/x-msmetafile',
        'wri'       => 'application/x-mswrite',
        'xbm'       => 'image/x-xbitmap',
        'xls'       => 'application/vnd.ms-excel',
        'xpm'       => 'image/x-xpixmap',
        'xwd'       => 'image/x-xwindowdump',
        'z'         => 'application/x-compress',
        'zip'       => 'application/zip',
        'apk'       => 'application/vnd.android.package-archive',
        'xap'       => 'application/x-silverlight-app',
        'ipa'       => 'application/vnd.iphone',
        'md'        => 'text/markdown',
        'xml'       => 'text/xml',
        'webp'      => 'image/webp',
        'png'       => 'image/png',
    ];

    private function __construct()
    {
    }

    /**
     * 获取扩展名对应的 Content-Type.
     */
    public static function getContentType(string $extension): string
    {
        return self::$extMap[$extension] ?? self::APPLICATION_OCTET_STREAM;
    }
}
