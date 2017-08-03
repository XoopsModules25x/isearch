<?php
/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Admin page footer file
 *
 * @package   module\isearch\admin
 * @author    Magic.Shao <magic.shao@gmail.com>, Susheng Yang <ezskyyoung@gmail.com>
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     ::   1.91
 *
 * @see       Xmf\Module\Admin
 */
echo "<div class='adminfooter'>\n"
     . "<div class='center'>\n"
     . "<a href='http://www.xoops.org' rel='external' target='_blank'>"
     . "<img src='"
     . Xmf\Module\Admin::iconUrl('xoopsmicrobutton.gif', '32')
     . "' "
     . "alt='XOOPS' title='XOOPS'></a>\n"
     . "</div>\n"
     . _AM_MODULEADMIN_ADMIN_FOOTER
     . "\n"
     . "</div>\n";

xoops_cp_footer();
