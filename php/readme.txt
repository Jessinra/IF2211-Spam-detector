ada dua api untuk twitter.php

contohnya twitter.php?first=false&keyword=jokowi&spam=jokowi&result_type=popular&algo=KMP&max_id=986550822157729791&include_entities=1


first : wajib {true|false}
	yang dimaksud first adalah request pertama, jika false artinya ingin mengambil next result dari tweet misal ada 70 tweet pencarian, setiap request akan mengembalikan 
	15 tweet, nah jika ingin melakukan next request set first menjadi false, lalu isi parameter max_id,include_entitites dan akan mengembalikan 15 tweet selanjutnya, jika habis maka kembalian jsonnya tidak ada max_id
keyword : wajib
spam : wajib {diseparasi dengan koma contoh "wildan,ganteng"}
result_type : wajib {popular|mixed|recent}
algo : wajib {KMP|Regex|BoyerMoore}
max_id : wajib jika first = false
include_entities : wajib jika first = false