// require('@uppy/core/dist/style.min.css')
// require('@uppy/drag-drop/dist/style.min.css')
// require('@uppy/status-bar/dist/style.min.css')
require('alpinejs');

import Uppy from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import AwsS3Multipart from '@uppy/aws-s3-multipart'

window.Uppy = Uppy
window.Dashboard = Dashboard
window.AwsS3Multipart = AwsS3Multipart
