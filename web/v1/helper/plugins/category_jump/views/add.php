<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>admin</title>
        <link href="<?php echo ADMIN_THEME ?>images/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo ADMIN_THEME ?>images/system.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo ADMIN_THEME ?>images/main.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo ADMIN_THEME ?>images/table_form.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo ADMIN_THEME ?>images/dialog.css" rel="stylesheet" type="text/css" />
        <script language="javascript" src="<?php echo ADMIN_THEME ?>js/jquery.min.js"></script>
    </head>
    <body>
        <div class="subnav">
            <div class="content-menu ib-a blue line-x">
                <?php
                foreach ($menu as $i => $t) {
                    $class = $_GET['a'] == $t[0] || $_GET['a'] == 'edit' && $t[0] == 'add' ? ' class="on"' : '';
                    $span = $i >= count($menu) - 1 ? '' : '<span>|</span>';
                    echo '<a href="' . purl('admin/' . $t[0]) . '" ' . $class . '><em>' . $t[1] . '</em></a>' . $span;
                }
                ?>
            </div>
            <div class="bk10"></div>
            <div class="table-list">
                <form action="" method="post">
                    <table width="100%" class="table_form">
                        <tbody>
                            <tr>
                                <th width="200">名称：</th>
                                <td><input type="text" name="data[subject]" size=50 class="input-text" value="<?php echo $data['subject'] ?>"/></td>
                            </tr>
                            <tr>
                                <th>分类选择：</th>
                                <td>
                                    <select name="data[cat_id]" id="catid">
                                        <?php echo $category ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>范围：</th>
                                <td>开始：<input name="data[start]" size=5 class="input-text"  type="input" value="<?php echo $data['start'] ?>" /> 
                                    &nbsp;~&nbsp;&nbsp;
                                    结束：<input name="data[end]" size=5 class="input-text"  type="input" value="<?php echo $data['end'] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>描述：</th>
                                <td><textarea style="width:400px;height:100px;" name="data[description]"><?php echo $data['description'] ?></textarea></td>
                            </tr>
                            <tr>
                                <th></th>
                                <td><input type="submit" class="button" value="提 交" name="submit"></td>
                            </tr>
                            <tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </body>
</html>
<script language="javascript">
    function add_option() {
        var id = parseInt(Math.random() * 1000);
        var html = '<tr id=o_' + id + '><th width="200"><input type="text" name="data[options][]" size=30 class="input-text" value=""> </th><td>&nbsp;<a href="javascript:removediv(\'' + id + '\');">移除</a></td></tr>';
        $('#vote_options').append(html);
    }
    function removediv(k) {
        $('#o_' + k).remove();
    }
</script>