<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>primeRental</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        body {
            font-family: 'figtree', sans-serif;
        }
        .header {
            background-color: #ffffff;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .section {
            padding: 4rem 0;
        }
        .section img {
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .section h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .section p {
            color: #718096;
            line-height: 1.75;
            margin-bottom: 1.5rem;
        }
        .section button {
            padding: 0.75rem 1.5rem;
            background-color: #2d3748;
            color: #ffffff;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: background-color 0.3s;
        }
        .section button:hover {
            background-color: #38a169;
        }
        .why-us {
            background-color: #ffffff;
            text-align: center;
        }
        .why-us h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 1.5rem;
        }
        .why-us p {
            color: #718096;
            margin-bottom: 2rem;
        }
        .why-us .card {
            padding: 1.5rem;
            background-color: #f7fafc;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .why-us .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        .why-us .card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .why-us .card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        footer {
            text-align: center;
            font-size: 0.875rem;
            color: #a0aec0;
            margin-top: 2rem;
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('/home') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Home</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <header class="header">
                <div class="container mx-auto px-4">
                    <h1>Home / About Us</h1>
                </div>
            </header>

            <section class="section">

                    <div class="text-center md:text-left">
                        <h2>Property Selling and Renting</h2>
                        <p>
                            Prime Rental House Sell and Rent is a comprehensive platform designed to simplify the process of buying, selling, and renting prime residential properties. It connects property owners with potential buyers or tenants, offering features like property listings, virtual tours, price comparisons, and secure transactions. With a focus on prime locations, the platform ensures high-quality listings and caters to individuals seeking luxurious, comfortable, and well-maintained homes. Whether you're looking to sell a property, find your dream home, or rent a house, Prime Rental House Sell and Rent provides a seamless and user-friendly experience.
                        </p>
                        <button onclick="https://primeaddis.com">Learn More</button>
                         <a href="https://primeaddis.com" class="inline-block mt-4 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-300">Visit primeaddis.com</a>
                    </div>
                </div>
            </section>

            <section class="section why-us">
                <div class="container mx-auto px-4">
                    <h2>Why Us</h2>
                    <p>Best real estate agents you will ever see in your life. If you encounter any problems, do not hesitate to knock on our agents.</p>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        @foreach ([
                            ['title' => 'Wide Range of Properties', 'icon' => '🏠'],
                            ['title' => 'Finest Community', 'icon' => '🌟'],
                            ['title' => 'Investment', 'icon' => '💼'],
                            ['title' => 'Homes That Match', 'icon' => '🏡'],
                        ] as $item)
                            <div class="card">
                                <div class="icon">{{ $item['icon'] }}</div>
                                <h3>{{ $item['title'] }}</h3>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <footer></footer>
                <div class="container mx-auto px-4">
                    <p>&copy; 2021 primeRental. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
