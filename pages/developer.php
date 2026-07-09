<?php
// Developer page displaying specific listen sockets information as requested
?>

<div class="alert alert-info bg-info bg-opacity-10 border border-info border-opacity-20 text-info py-2.5 px-3 fs-8 mb-4 rounded-10 animated-fade-in" role="alert">
    <span><i class="fi fi-sr-info me-2 align-middle"></i>Daftar port aktif yang dikelola oleh user (Hasil perintah: <code>ss -lptn</code>)</span>
</div>

<div class="row g-4">
    <!-- PHP Web Server Port 80 Card -->
    <div class="col-12 col-md-6 col-lg-4 animate-fade-in">
        <div class="glass-card h-100 d-flex flex-column justify-content-between p-4">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-container icon-primary mb-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                        <i class="fi fi-sr-computer"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 border border-success border-opacity-25 text-success px-2 py-1 fs-9">
                        LISTEN
                    </span>
                </div>
                
                <h5 class="text-white font-weight-700 mb-1">php (Web UI)</h5>
                <p class="text-secondary fs-8 mb-4">Layanan utama antarmuka pengguna web.</p>
                
                <div class="table-responsive">
                    <table class="table table-borderless text-white fs-8 mb-0">
                        <tbody>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="text-secondary ps-0 py-1.5">Local Address</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500 text-info">0.0.0.0:80</td>
                            </tr>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="text-secondary ps-0 py-1.5">Process PID</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500">2773</td>
                            </tr>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="text-secondary ps-0 py-1.5">Queue (Send/Recv)</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500">0 / 128</td>
                            </tr>
                            <tr>
                                <td class="text-secondary ps-0 py-1.5">FD (File Desc)</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500">3</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Android IP Camera Port 4444 Card -->
    <div class="col-12 col-md-6 col-lg-4 animate-fade-in">
        <div class="glass-card h-100 d-flex flex-column justify-content-between p-4">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-container icon-warning mb-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                        <i class="fi fi-sr-camera"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 border border-success border-opacity-25 text-success px-2 py-1 fs-9">
                        LISTEN
                    </span>
                </div>
                
                <h5 class="text-white font-weight-700 mb-1">androidipcamera</h5>
                <p class="text-secondary fs-8 mb-4">Layanan streaming kamera perangkat mobile.</p>
                
                <div class="table-responsive">
                    <table class="table table-borderless text-white fs-8 mb-0">
                        <tbody>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="text-secondary ps-0 py-1.5">Local Address</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500 text-warning">*:4444</td>
                            </tr>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="text-secondary ps-0 py-1.5">Process PID</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500">6526</td>
                            </tr>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="text-secondary ps-0 py-1.5">Queue (Send/Recv)</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500">0 / 50</td>
                            </tr>
                            <tr>
                                <td class="text-secondary ps-0 py-1.5">FD (File Desc)</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500">51</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- oLibV2RayDaemon Port 10808 Card -->
    <div class="col-12 col-md-6 col-lg-4 animate-fade-in">
        <div class="glass-card h-100 d-flex flex-column justify-content-between p-4">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-container icon-info mb-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                        <i class="fi fi-sr-wifi"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 border border-success border-opacity-25 text-success px-2 py-1 fs-9">
                        LISTEN
                    </span>
                </div>
                
                <h5 class="text-white font-weight-700 mb-1">oLibV2RayDaemon</h5>
                <p class="text-secondary fs-8 mb-4">Layanan Daemon proxy jaringan V2Ray.</p>
                
                <div class="table-responsive">
                    <table class="table table-borderless text-white fs-8 mb-0">
                        <tbody>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="text-secondary ps-0 py-1.5">Local Address</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500 text-info">*:10808</td>
                            </tr>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="text-secondary ps-0 py-1.5">Process PID</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500">6319</td>
                            </tr>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="text-secondary ps-0 py-1.5">Queue (Send/Recv)</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500">0 / 128</td>
                            </tr>
                            <tr>
                                <td class="text-secondary ps-0 py-1.5">FD (File Desc)</td>
                                <td class="text-end pe-0 py-1.5 font-weight-500">57</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
