function display_investment_chart() {
    // Incluir Chart.js
    $chart_js_url = 'https://cdn.jsdelivr.net/npm/chart.js';
    wp_enqueue_script('chart-js', $chart_js_url, array(), null, true);

    // Ruta del archivo numeros-chart.js (asegúrate de que esta ruta sea correcta)
    $numeros_chart_url = get_home_url() . '/wp-content/numeros-chart.js'; // Cambia esta ruta si el archivo está en otro lugar

    // Contenedor del gráfico y script
    ob_start();
    ?>
    <div style="width: 100%; max-width: 700px; margin: auto;">
        <canvas id="investmentChart"></canvas>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar si Chart.js está cargado
            if (typeof Chart === 'undefined') {
                console.error('Chart.js no está cargado. Asegúrate de que la librería esté disponible.');
                return;
            }

            // Cargar el archivo numeros-chart.js y generar el gráfico
            const numerosChartUrl = '<?php echo esc_url($numeros_chart_url); ?>';

            fetch(numerosChartUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error al cargar el archivo: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const labels = data.rentabilidad_acumulada.map(entry => 'Mes ' + entry.mes);
                    const investmentValues = data.rentabilidad_acumulada.map(entry => entry.inversion);
                    const profitValues = data.rentabilidad_acumulada.map(entry => entry.rentabilidad_acum);

                    const ctx = document.getElementById('investmentChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Inversión (COP)',
                                    data: investmentValues,
                                    borderColor: 'blue',
                                    fill: false
                                },
                                {
                                    label: 'Rentabilidad Acumulada (COP)',
                                    data: profitValues,
                                    borderColor: 'green',
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Mes'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Valor en COP'
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error al cargar el archivo numeros-chart.js:', error));
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('estadistico_migben', 'display_investment_chart');
