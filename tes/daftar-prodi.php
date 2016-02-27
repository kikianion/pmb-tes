<div style="text-align: center; width: 100%;"><h3>Master Program Studi</h3></div>

<?php
$ds="master-prodi-ds";

$master_prodi=load_prodiname();

?>
<div style="padding: 10px;">
<?php
//hot compoonent template 
include "_comp/_hot-single.php";
?>
<pre>
* Kode Program Studi setelah ditentukan dan dipakai dalam tes, tidak boleh dirubah
* Bila dirubah setelah terpakai akan mengakibatkan prodi tidak diketahui
</pre>
</div>

<script type="text/javascript">

    var master_prodi=<?php echo json_encode($master_prodi)?>;

    var hot_options={
        colHeaders: [ '', 'Kode','Nama',],
        colWidths: [30,150,300,],
        columns: [
            {
                readOnly: true,
                renderer: htmlRenderer1,
            },
            {
                renderer: colorRenderer,
            },
            {
                renderer: colorRenderer,
            },
        ],
        beforeRemoveRow: function(index, amount){
            hot_on_before_remove_row(index, amount);
            return false;
        },
        afterCreateRow: function(index,amount){
        },
        afterChange: function (change, source) {
            hot_on_after_change(change, source);
        }                
    }

    $(function(){
        build_hot(
            function(hot){

        });

        load_hot_data(
            function(res){
                //debugger;
                var data = [], row;

                for (var i = 0, ilen = res.data.length; i < ilen; i++) {
                    row = [];
                    row[0] = parseInt(res.data[i].id);
                    row[1] = res.data[i].kode;
                    row[2] = res.data[i].nama;
                    
                    var prodiname_rs=alasql("select nama from ? where kode='"+res.data[i].prodi+"' ",[master_prodi])
                    var prodiname=prodiname_rs.length>0?prodiname_rs[0].nama:'-';                   
                    row[3] = prodiname;
                    
                    row[4] = res.data[i].nilai;

                    data[i] = row;
                }
                return data;

            },
            function(textStatus, errorThrown ){
                flashMessage(textStatus+": "+errorThrown);
            },
            null
        );

    });
    
    
    
</script>

<?php
    function load_prodiname(){
        global $pdoc;
        $s="select * from prodi";
        $st=$pdoc->prepare($s);
        $st->execute();
        
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
?>