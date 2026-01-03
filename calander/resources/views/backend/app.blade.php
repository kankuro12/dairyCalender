<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<style>
    .grid {
        display: grid;
        grid-template-columns: 0.2fr 2fr;
        gap: 10px;
    }

    .sidebar {
        background-color: #f4f4f4;
        padding: 15px;
        height: 100vh;
    }

    a {
        text-decoration: none;
        color: blue;
    }
</style>

<body>
    <h1>Welcome to the Admin Panel</h1>
    <div class="containerfluid">
        <div class ='grid'>
            <div class="sidebar">
                <h3>Sidebar</h3>
            </div>
            <div class="main-content">


                @php
                    $month = [
                        'बैशाख',
                        'जेष्ठ',
                        'आषाढ',
                        'श्रावण',
                        'भाद्र',
                        'आश्विन',
                        'कार्तिक',
                        'मंसिर',
                        'पौष',
                        'माघ',
                        'फाल्गुन',
                        'चैत्र',
                    ];
                @endphp
                <form method ="GET" action="#" id="monthYearForm">
                    <button class="btn btn-primary" type="submit" id="addEventBtn">Add Event</button>
                    <select id="monthSelect">
                        <option value="">Select Month</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ $month[$i - 1] }}</option>
                        @endfor

                    </select>
                    <select id="yearSelect">
                        <option value="">Select Year</option>
                        @for ($year = 2075; $year <= 2085; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </form>
                {{-- 
                    @for ($i = 1; $i <= 12; $i++)
                        <tr>

                            <td>{{ $i }}</td>
                            <td>{{ $month[$i - 1] }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <a href="#" class="add-month" data-month="{{ $i }}">Add Events in the
                                    Month</a>
                            </td>

                        </tr>
                    @endfor --}}

            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
            integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
        </script>
        <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
        <script src={{ asset('js/nepali.datepicker.min.js') }}></script>
        <script>
            //month ko days ni patako 
            document.querySelectorAll('.add-month').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const month = this.dataset.month;
                    const daysInMonth = NepaliFunctions.BS.GetDaysInMonth(2082, month);
                    const url = `/admin/add-month-data/${month}?days=${daysInMonth}`;
                    window.location.href = url;
                });
            });
            document.getElementById('monthYearForm').onsubmit = function(e) {
                e.preventDefault();
                const month = document.getElementById('monthSelect').value;
                const year = document.getElementById('yearSelect').value;
                if (!month || !year) {
                    alert('Please select both month and year');
                    return;
                }
                const daysInMonth = NepaliFunctions.BS.GetDaysInMonth(year, month);
                const url = `/admin/add-month-data/${month}?year=${year}&days=${daysInMonth}`;
                window.location.href = url;
            }

            //dont know k garxa vanera surma lekeko thiye paxi hatauda vayo herera
            // let calendarData = [];
            // const addEventBtn = document.getElementById('addEventBtn');
            // addEventBtn.onclick = function() {
            //     const promt = prompt('Do you want to load the event yes/No');
            //     console.log(promt);
            //     if (promt.toLowerCase() === 'yes') {

            //         $.ajax({
            //             url: '/admin/load-event',
            //             type: 'GET',
            //             success: function(response) {
            //                 console.log(response);
            //                 const arr1 = response.calendarSummary[0].days;
            //                 const arr2 = response.calendarSummary[1].days;
            //                 console.log(arr1);
            //                 console.log(arr2);
            //                 calendarData = response.calendarSummary;
            //                 alert('Event Loaded Successfully');
            //             },
            //             error: function(xhr) {
            //                 console.log(xhr.responseText);
            //                 alert('Error loading events ' +
            //                     xhr.message);
            //             }
            //         });


            //     } else {
            //         alert('Event Loading Cancelled');
            //     }
            // }
        </script>
</body>

</html>
