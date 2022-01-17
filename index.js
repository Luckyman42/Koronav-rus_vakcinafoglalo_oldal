function drawCalendar(newMonth) {
    let loading = document.querySelector("#loading");
    let calendar = document.querySelector("#calendar");
    let actualRow = document.createElement("tr");
    actualRow.className = "calendarRow";
    let tdCount = 0;

    let rowsOfCalendar = document.querySelectorAll(".calendarRow")
    for (let calendarRow of rowsOfCalendar) {
        calendarRow.remove();
    }

    loading.style.display = "none";

    for(let i = 1; i <= newMonth.offset; i++)  {
        let td = document.createElement("td");
        td.className="calendarTD";
        actualRow.appendChild(td);
        tdCount++;
    }

    for(let day = 1; day <= newMonth.numberOfDays; day++) {
        let td = document.createElement("td");
        td.className="calendarTD";

        let indexOfDay = document.createElement("p");
        indexOfDay.innerText = day;

        
        let p = document.createElement("p");
        td.appendChild(indexOfDay);
        
        if(newMonth.days[day-1].length != 0){
            let dataOfDay = document.createElement("div");
            for (let i = 0; i < newMonth.days[day-1].length; i++) {
                let appointment = document.createElement("p");
                let span = document.createElement("span");
                span.innerHTML = newMonth.days[day-1][i].hour+":"+newMonth.days[day-1][i].min+" --- "+ newMonth.days[day-1][i].limit + "/" +newMonth.days[day-1][i].users.length +"    " ;
                appointment.appendChild(span);
                if( (newMonth.days[day-1][i].limit > newMonth.days[day-1][i].users.length )&& !user.haveAppoi ){
                    let link = document.createElement("a");
                    if(user.isAdmin){
                        link.innerText = "Részletek";
                    }else{
                        link.innerText = "Jelentkezés";
                    }
                    link.href = "oneAppoi.php?appid="+newMonth.days[day-1][i].id;
                    appointment.appendChild(link);
                    
                    if(newMonth.days[day-1][i].limit > newMonth.days[day-1][i].users.length){
                        appointment.classList.add("notfull");
                    }else{
                        appointment.classList.add("full");
                    }
                }
                dataOfDay.appendChild(appointment);
            }
            td.appendChild(dataOfDay);
  
        }
        


        actualRow.appendChild(td);
        tdCount++;

        if (tdCount % 7 == 0) {
            calendar.appendChild(actualRow);
            actualRow = document.createElement("tr");
            actualRow.className = "calendarRow";

        }
    }

    let calc = 7 - (tdCount % 7);

if(tdCount % 7 != 0 ){
    while(tdCount % 7 != 0){
        let td = document.createElement("td");
        td.className="calendarTD";
        actualRow.appendChild(td);
        tdCount++;
    }
    calendar.appendChild(actualRow);
}
    refreshHeader();
}


function getUserData() {
    return fetch('calendar_fun.php?fun=getUserData', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body:""
    })
        .then(answer => answer.json())
        .then(x => x)
}

function getCalendar(m, y) {
    return fetch('calendar_fun.php?fun=getCalendar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            month: m,
            year: y
        })
    })
        .then(answer => answer.json())
        .then(x => x)
}


function getServerDate() {
    return fetch('calendar_fun.php?fun=current', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: ""
    })
        .then(answer => answer.json())
        .then(x => x)
}
function refreshHeader() {
    let mounthName = "";
    switch(currentCalendar.month) {
        case 1: mounthName = "Január"; break;
        case 2: mounthName = "Február"; break;
        case 3: mounthName = "Március"; break;
        case 4: mounthName = "Április"; break;
        case 5: mounthName = "Május"; break;
        case 6: mounthName = "Június"; break;
        case 7: mounthName = "Július"; break;
        case 8: mounthName = "Augusztus"; break;
        case 9: mounthName = "Szeptember"; break;
        case 10: mounthName = "Október"; break;
        case 11: mounthName = "November"; break;
        case 12: mounthName = "December"; break;
        default:
            mounthName = "SAJT";
    }
    document.querySelector("#calHeader").innerHTML = currentCalendar.year + " " + mounthName;
}

let currentCalendar;
let serverDate;
let user;
let prevBtn = document.querySelector("#prev");
let nextBtn = document.querySelector("#next");

prevBtn.addEventListener('click', () => {
    let year = currentCalendar.year;
    let month = currentCalendar.month;

    if (month === 1) {
        month = 12;
        year--;
    }
    else {
        month--;
    }

    getCalendar(month, year).then(calendar => {
        currentCalendar = calendar;
        drawCalendar(currentCalendar);
    });

});
nextBtn.addEventListener('click', () => {
    let year = currentCalendar.year;
    let month = currentCalendar.month;

    if (month === 12) {
        year++;
        month = 1;
    }
    else {
        month++;
    }

    getCalendar(month, year).then(calendar => {
        currentCalendar = calendar;
        drawCalendar(currentCalendar);
    });

})

getUserData().then(x => {
    console.log("cica:: " + x);
    user = x}
    );

getServerDate().then(date => {
    serverDate = date;
    getCalendar(date.month, date.year).then(calendar => {
        console.log(calendar);
        currentCalendar = calendar;
        console.log(calendar); //----------------------------------------------------
         drawCalendar(currentCalendar);
    });
})

