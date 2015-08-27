<?php
ini_set("memory_limit","-1");

//遍历目标目录中的文件
function searchDir($path,&$data){
    if(is_dir($path)){
    $dp=dir($path);
    while($file=$dp->read()){
        if($file!='.'&& $file!='..'){
            searchDir($path.'/'.$file,$data);
        }
    }
    $dp->close();
    }

    if(is_file($path)){
    $data[]=$path;
    }
}

//获取目标文件类型
function getExtension($file) { 
    return pathinfo($file, PATHINFO_EXTENSION); 
}

//建立与数据库的连接
function connectToDatabase(){
    //建立mysql数据库连接
    $con=mysqli_connect("localhost","root","","test2");
    // 检查连接
    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    return $con;
}

//对数据库进行操作
function excuteSqlAction($dir){
    //建立与数据库的连接
    $con = connectToDatabase();
    $data=array();
    //遍历目标目录中的文件
    searchDir($dir,$data);
    $count = 0;
    // $max = 0;
    foreach ($data as $filepath) {
        $filename = basename($filepath);
        $fileExt = getExtension($filepath);
        switch ($fileExt) {
            case 'sql':
                $count = $count + 1;
                // echo "serial ".$count.": ".$filepath."\r\n";
                echo "Putting ".$filename." into database...\r\n";
                // $fileSize = filesize($filepath);
                // echo $filepath.' '.$fileSize.'   ';
                // if($fileSize > $max){
                //     $max = $fileSize;
                // }
                if(filesize($filepath) < 300*1024*1024){
                    $count = $count + 1;
                    $sql = file_get_contents($filepath);
                    $a = explode(';', $sql);
                    foreach ($a as $b) {
                        $c = $b.';';
                        mysqli_query($con,$c);
                        // echo $c;
                    }   
                    echo "Database".$count." ".$filename." initializes successful!\r\n\r\n";
                }
                else{
                    echo "The file".$filename." is out of size,initialize failed!\r\n\r\n";
                }
                break;

            // case 'txt':
            //     $tableName = basename("$filepath",".txt");
            //     $pathinfo = pathinfo($filepath,PATHINFO_DIRNAME);
            //     echo $pathinfo;
            //     $lines = file_get_contents($filepath);
            //     $line = explode("\n",$lines);
            //     //根据文件名建表
            //     $sqlCreateTable = "CREATE TABLE $tableName 
            //                             (
            //                             personID int unsigned NOT NULL AUTO_INCREMENT, 
            //                             PRIMARY KEY(personID),
            //                             username varchar(51),
            //                             password varchar(32),
            //                             email varchar(50)
            //                             )DEFAULT CHARSET=utf8";
            //     mysqli_query($con,$sqlCreateTable);

            //     // $sqlInsertData = "INSERT INTO $tableName(username,password) VALUES";
            //     // foreach($line as $key => $li){
            //     //     $arr = explode(" ",$li);
            //     // }
            //     $str = "Hello  world. I love Shanghai!";
            //     print_r (explode("  ",$str));

            //     echo "find a txt file!\r\n";

            //     break;
            // test
            
            default:
                # doing nothing
                break;
        }
       
    // mysqli_query($con,"INSERT INTO allfile_name(filename,filename_path) VALUES ('$filename','$filepath')");
    }
}

//传递参数
excuteSqlAction("E:\data2");
?>