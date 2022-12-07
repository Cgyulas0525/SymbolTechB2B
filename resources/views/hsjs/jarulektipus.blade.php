<script type="text/javascript">
function kategoriafeltolt(data_viewer) {
    var strev;
    var van;
    var kategoria = [];
    for (i = 0; i < data_viewer.length; i++ ){
        strev = data_viewer[i].ev.toString();
        if (i == 0){
            kategoria.push(strev);
        }else{
            van = 0;
            for (j = 0; j < kategoria.length; j++){
                if ( kategoria[j] == strev){
                    van = 1;
                }
            }
            if (van == 0){
                kategoria.push(strev);
            }
        }
    }
    return kategoria;
}

function tipusfeltolt(data_viewer) {
    var tipustomb = [];
    var elemtipus;
    for (i=0; i < data_viewer.length; i++){
        elemtipus = data_viewer[i].tipus;
        if (i == 0){
            tipustomb.push(elemtipus);
        }else{
            van = 0;
            for (j = 0; j < tipustomb.length; j++){
                if ( tipustomb[j] == elemtipus){
                    van = 1;
                }
            }
            if (van == 0){
                tipustomb.push(elemtipus);
            }
        }
    }
    return tipustomb;
}

function osszegfeltolt(tipustomb, kategoria, data_viewer) {
    var elemtomb = [];
    var strev;
    var data_view = [];
    for (j=0; j<tipustomb.length; j++) {
        elemtomb = [];
        for (i=0; i<kategoria.length; i++){
            elemtomb.push(0);
        }
        for (i=0; i < data_viewer.length; i++){
            if (tipustomb[j] == data_viewer[i].tipus){
                strev = data_viewer[i].ev.toString();
                for (k=0; k < kategoria.length; k++){
                    if (kategoria[k] == strev){
                        elemtomb[k] = Math.round(data_viewer[i].osszeg);
                    }
                }
            }
        }
        elemtipus = tipustomb[j];
        data_view.push({name: elemtipus, data: elemtomb});
    }
    return data_view;
}

</script>
