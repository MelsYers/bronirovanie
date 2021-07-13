<!doctype html>
<html lang="en">
    <head>
    <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js" integrity="sha512-bZS47S7sPOxkjU/4Bt0zrhEtWx0y0CRkhEp8IckzK+ltifIIE9EMIMTuT/mEzoIMewUINruDBIR/jJnbguonqQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <title>Bron!</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        <div id="app" class="bg-dark w-100 bg-gradient" >
            <div class="text-center bg-light py-3" v-if="data!=''">
                <p class="m-0 fs-3">{{data.company}}</p>
                <p class="m-0">{{data.address}}</p>
                <a :href="'tel:'+data.number" class="link-dark" >{{data.number}}</a><br>
                <a v-if="isMobile()" :href="'geo:'+data.coords[0]+','+data.coords[1]" class="link-dark" >Открыть здание на карте</a>
            </div>

            <div class="container d-flex justify-content-center" v-if="stage==1">
                <div class="rounded my-5 bg-light w-100 p-5">
                    <p class="fs-3 m-0 fw-bold">{{h1}}</p>
                    <p class="fs-5">{{h2}}</p>
                    
                    <ul class="list-group mb-3">
                        <button @click.prevent="stage=3" class="list-group-item btn-outline-dark w-100 px-4 py-3 fs-4 d-flex justify-content-between">
                            <span><i class="bi bi-person"></i>  Выбрать мастера</span>
                            <i class="bi bi-caret-right"></i>
                        </button>
                        <div class="list-group-item" v-if="choosenPerson != ''">
                            <div>
                                <span class="fw-bold"><i class="bi bi-dash"></i>{{choosenPerson.name}}<i class="bi bi-dash"></i></span>
                            </div>
                        </div>
                    </ul>

                    <ul class="list-group mb-3" v-if="choosenPerson != ''">
                        <button @click.prevent="stage=2" class="list-group-item btn-outline-dark w-100 px-4 py-3 fs-4 d-flex justify-content-between ">
                            <span><i class="bi bi-brush"></i>  Выбрать услугу</span>
                            <i class="bi bi-caret-right"></i>
                        </button>
                        <div class="list-group-item" v-if="choosen.length>0">
                            <div v-for="item in choosen">
                                <span><i class="bi bi-dash"></i>{{item.name}}<i class="bi bi-dash"></i> <i class="bi bi-wallet2"></i> {{item.price}} тг. <i class="bi bi-clock-history"></i> {{item.time}} минут</span>
                                
                            </div>
                            <hr>
                            <span class="fw-bold"><i class="bi bi-dash"></i>Общее: <i class="bi bi-wallet2"></i> {{getPriceSum()}} тг <i class="bi bi-clock-history"></i> {{getTimeSum()}} минут</span>
                        </div>
                    </ul>


                    <ul class="list-group mb-3" v-if="choosenPerson != '' && choosen.length>0">
                        <button @click.prevent="stage=4" class="list-group-item btn-outline-dark w-100 px-4 py-3 fs-4 d-flex justify-content-between">
                            <span><i class="bi bi-clock"></i>  Выбрать время</span>
                            <i class="bi bi-caret-right"></i>
                        </button>
                        <div class="list-group-item" v-if="choosenTime != ''">
                            <span><i class="bi bi-calendar-event"></i> {{choosenTime[0].format("DD-MM-YYYY")}} <i class="bi bi-clock-history"></i> {{choosenTime[0].format("HH:mm")}} - {{choosenTime[1].format("HH:mm")}}</span>
                        </div>
                    </ul>

                    <div v-if="choosenTime!='' && choosenPerson != '' && choosen.length>0">
                        <p class="fs-3">Заполните данные пользователя</p>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Имя</span>
                            <input type="text" class="form-control" v-model="personName">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Телефон</span>
                            <input type="tel" class="form-control" v-model="tel">
                        </div>

                        <button v-if="personName != '' && tel != ''" class="btn btn-dark">Записаться</button>
                    </div>
                    
                </div>
            </div>

            <div class="container d-flex justify-content-center" v-if="stage==2">
                <div class="rounded my-5 bg-light w-100 p-5">
                    <button class="btn btn-outline-dark border-0 mb-3" @click.prevent="stage=1"><i class="bi bi-caret-left"></i></button>

                    <div class="row w-100">
                        <div class="col-md-3">
                            <div v-for="item in programs" class="">
                                <div :class="item==choosenCat ? 'w-100 px-3 btn btn-dark mb-4' : 'w-100 px-3 mb-4 btn btn-outline-dark'" @click.prevent="choosenCat=item">
                                    {{item.name}}
                                </div>
                            </div>
                            <div class="text-center" v-if="choosen.length>0">
                                <div v-for="item in choosen" class="mb-1">
                                    <span class="text-truncate"><i class="bi bi-dash"></i> {{item.name}}<i class="bi bi-dash"></i> </span><br>
                                    <span><i class="bi bi-wallet2"></i> {{item.price}}</span> тг <i class="bi bi-clock-history"></i> {{item.time}} минут
                                    <hr>
                                </div>
                                <span class="fw-bold">Общее: {{getPriceSum()}}тг {{getTimeSum()}} минут</span>
                            </div>
                        </div>
                        <div class="col-md-9 d-flex flex-column align-items-center">
                            <div v-for="(service, index) in choosenCat.service" class="mb-4">
                                <!--<button class="btn btn-outline-dark me-1" @click.prevent="choosen = []; choosen.push(service); stage=1">
                                    <i class="bi bi-dash"></i> {{service.name}} <i class="bi bi-dash"></i><br>
                                    <i class="bi bi-wallet2"></i> {{service.price}} тг.
                                </button>-->
                                
                                <input type="checkbox" class="btn-check" :value="service" v-model="choosen" :id="'service'+index">
                                <label class="btn btn-outline-dark py-3 px-5" :for="'service'+index">
                                    <span><i class="bi bi-dash"></i> {{service.name}} <i class="bi bi-dash"></i></span><br>
                                    <div class="d-flex justify-content-between">
                                        <span class="fs-6"><i class="bi bi-wallet2"></i> {{service.price}} тг.</span>
                                        <span class="fs-6"><i class="bi bi-clock-history"></i> {{service.time}} минут</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container d-flex justify-content-center" v-if="stage==3">
                <div class="rounded my-5 bg-light w-100 p-5">
                    <button class="btn btn-outline-dark border-0 mb-3" @click.prevent="stage=1"><i class="bi bi-caret-left"></i></button>

                    <div v-for="item in persons" class="border rounded mb-2 p-3" @click="choosenPerson=item; stage=1">
                        <p class="fs-4">{{item.name}}</p>
                        <p class="text-secondary">На сегодня</p>
                        <span v-for="today in item.days" v-if="today.day==currentDate()">
                            <button class="btn btn-outline-dark me-1" @click.prevent="choosenTime=times;stage=1;" v-for="times in getTodaysTime(today.times,item.divider, today.rests)">
                                {{times[0].format('HH')}}:{{times[0].format('mm')}} - {{times[1].format('HH')}}:{{times[1].format('mm')}}
                            </button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="container d-flex justify-content-center" v-if="stage==4">
                <div class="rounded my-5 bg-light w-100 p-5">
                    <button class="btn btn-outline-dark border-0 mb-3" @click.prevent="stage=1"><i class="bi bi-caret-left"></i></button>

                    <input type="date" v-model="todaysDate" @change="getTimeByHours()" class="form-control mb-3">

                    <div v-if="activeTimes.length>0">
                        <button v-for="times in activeTimes" class="btn btn-outline-dark me-1 mb-1" @click.prevent="choosenTime=times;stage=1">
                            {{times[0].format('HH')}}:{{times[0].format('mm')}} - {{times[1].format('HH')}}:{{times[1].format('mm')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>






        <script>
            var app = new Vue({
                el: '#app',
                data: {
                    personName: '',
                    tel: '',
                    choosenTime: '',
                    activeTimes: [],
                    todaysDate: '',
                    choosenPerson: '',
                    choosenCat: '',
                    choosen: [],
                    stage: 1,
                    data: '',
                    h1: 'Онлайн запись',
                    h2: 'Выберите, с чего вы хотите начать',
                    programs: [
                        {
                            "name": "Ресницы",
                            "service": [
                                {
                                    "name": "Наращивание ресниц 1",
                                    "price": 5000,
                                    "time": 40,
                                    "person_id": 1
                                },
                                {
                                    "name": "Наращивание ресниц 2",
                                    "price": 4000,
                                    "time": 50,
                                    "person_id": 1
                                },
                                {
                                    "name": "Наращивание ресниц 3",
                                    "price": 4500,
                                    "time": 60,
                                    "person_id": 2
                                }
                            ]
                        },
                        {
                            "name": "Брови",
                            "service": [
                                {
                                    "name": "Наращивание бровей 1",
                                    "price": 8000,
                                    "time": 55,
                                    "person_id": 1
                                },
                                {
                                    "name": "Наращивание бровей 2",
                                    "price": 9000,
                                    "time": 50,
                                    "person_id": 2
                                },
                                {
                                    "name": "Наращивание бровей 3",
                                    "price": 10000,
                                    "time": 30,
                                    "person_id": 2
                                }
                            ]
                        }
                    ],
                    persons: [
                        {
                            "id": 1,
                            "name": "Азат Мамыканов",
                            "days": [
                                {
                                    "day": 1,
                                    "times": ["08:00", "20:00"],
                                    "rests": 
                                        ["13:00", "14:00"]
                                    
                                },
                                {
                                    "day": 2,
                                    "times": ["08:00", "20:00"],
                                    "rests": 
                                        ["13:00", "14:00"]
                                    
                                },
                                {
                                    "day": 3,
                                    "times": ["08:00", "20:00"],
                                    "rests": 
                                        ["13:00", "14:00"]
                                    
                                },
                                {
                                    "day": 4,
                                    "times": ["08:00", "20:00"],
                                    "rests": 
                                        ["13:00", "14:00"]
                                    
                                },
                                {
                                    "day": 5,
                                    "times": ["08:00", "20:00"],
                                    "rests": 
                                        ["13:00", "14:00"]
                                    
                            
                                }
                            ],
                            "divider": 60
                        },
                        {
                            "id": 2,
                            "name": "Темирханулы Нурдаулет",
                            "days": [
                                {
                                    "day": 1,
                                    "times": ["09:00", "20:00"],
                                    "rests": 
                                        ["13:00", "13:30"]
                                
                                },
                                {
                                    "day": 2,
                                    "times": ["09:00", "20:00"],
                                    "rests": 
                                        ["13:00", "13:30"]
                                    
                                },
                                {
                                    "day": 3,
                                    "times": ["09:00", "20:00"],
                                    "rests": 
                                        ["13:00", "13:30"]
                                    
                                }
                            ],
                            "divider": 60
                        }
                    ]
                },
                created: function(){
                    axios.get("getData.php").then(response => {
                        this.data = response.data
                        console.log(this.data)
                    })

                    this.choosenCat = this.programs[0]
                },
                methods:{
                    getPriceSum(){
                        var sum=0
                        for(var i=0;i<this.choosen.length;i++){
                            sum+=this.choosen[i].price
                        }
                        return sum
                    },
                    getTimeSum(){
                        var sum=0
                        for(var i=0;i<this.choosen.length;i++){
                            sum+=this.choosen[i].time
                        }
                        return sum
                    },
                    isMobile() {
                        if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                            return true
                        } else {
                            return false
                        }
                    },
                    currentDate() {
                        const current = new Date();
                        return current.getDay();
                    },
                    getTodaysTime(times, divider, rest){
                        var now = moment()

                        var today = now.format("YYYY-MM-DD");
                        
                        var startTime = moment(today + " "+times[0], 'YYYY-MM-DD HH:mm')
                        var endTime = moment(today + " "+times[1], 'YYYY-MM-DD HH:mm')

                        var rests = []

                        if(startTime.diff(now)<0){
                            var nowTime = now.add(1, 'h').startOf('hour')
                            startTime = nowTime

                            if(rest.length>0){
                                rests = [moment(today+" "+rest[0], 'YYYY-MM-DD HH:mm'),moment(today +" "+rest[1], 'YYYY-MM-DD HH:mm')]
                            }
                        }

                        var arrayTime = []

                        
                        while(startTime.diff(endTime)<0){
                            
                            if(rests.length>0){
                                console.log(startTime.diff(rests[0]), ">0")
                                console.log(startTime.clone().add(divider, 'minute').diff(rests[0]), "<0")
                                var endRest = startTime.clone()
                                endRest.add(divider, 'minute')
                                if(startTime.diff(rests[0])>=0 && endRest.diff(rests[1])<=0){
                                    startTime.add(divider, 'minute')
                                    continue
                                }
                            }


                            var start = []
                            var startMoment = startTime.clone() 
                            start.push(startMoment)
                            var end = startMoment.clone()
                            start.push(end.add(divider, 'minute'))

                            startTime.add(divider, 'minute')

                            arrayTime.push(start)
                        }

                        return arrayTime;
                    },
                    getTimeByHours(){
                        this.activeTimes = []
                        var now = moment()

                        var today = now.format("YYYY-MM-DD HH:mm")

                        var zapis = moment(this.todaysDate, "YYYY-MM-DD")
                        var zapisDay = zapis.day()
                        
                        var elementDay = null
                        
                        var startTime = null//moment(todaysDate, 'YYYY-MM-DD HH:mm')
                        var endTime = null//moment(todaysDate, 'YYYY-MM-DD HH:mm')
                        
                        var rests = []


                        this.choosenPerson.days.forEach(element => {
                            if(element.day==zapisDay){
                                //rests = element.rests
                                elementDay = element.day
                                startTime = moment(this.todaysDate +" "+element.times[0], 'YYYY-MM-DD HH:mm')
                                endTime = moment(this.todaysDate +" "+element.times[1], 'YYYY-MM-DD HH:mm')
                                if(element.rests.length>0){
                                    rests = [moment(this.todaysDate +" "+element.rests[0], 'YYYY-MM-DD HH:mm'),moment(this.todaysDate +" "+element.rests[1], 'YYYY-MM-DD HH:mm')]
                                }
                                return
                            }
                        })

                        if(elementDay){
                            if(startTime.diff(now)<0){
                                var nowTime = now.add(1, 'h').startOf('hour')
                                startTime = nowTime
                            }

                            var arrayTime = []

                            while(startTime.diff(endTime)<0){
                                if(rests.length>0){
                                    var endRest = startTime.clone()
                                    endRest.add(this.choosenPerson.divider, 'minute')
                                    if(startTime.diff(rests[0])>=0 && endRest.diff(rests[1])<=0){
                                        startTime.add(this.choosenPerson.divider, 'minute')
                                        continue
                                    }
                                }

                                var start = []
                                var startMoment = startTime.clone() 
                                start.push(startMoment)
                                var end = startMoment.clone()
                                start.push(end.add(this.choosenPerson.divider, 'minute'))

                                startTime.add(this.choosenPerson.divider, 'minute')

                                arrayTime.push(start)
                            }
                            this.activeTimes = arrayTime
                        }

                        return null
                    }
                }
            })

        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>