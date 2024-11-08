<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="container-xxl" id="contact">
                    <div class="container">
                        <div class="text-center">
                            <h5 class="text-primary-gradient fw-medium">Get In Touch!</h5>
                            {{-- <h1 class="mb-5">Get In Touch!</h1> --}}
                            <h1 class="mb-3">Ready to Transform Your Business?
                            </h1>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="">
                                    <p class="text-center">Experience the power of a truly customizable CRM and ERP
                                        solution
                                        with Business Joy. Sign up now to be the first to access exclusive features and
                                        join the
                                        businesses that are redefining productivity and growth.</p>
                                    <form action="{{ route('admin.new') }}" id="modal-contactform" class="bj-landing-forms" method="Post">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" maxlength="30"
                                                        id="name" name="name" placeholder="Your Name" required>
                                                    <label for="name">Your Name*</label>
                                                    <span class="error-msg" id="error-name" style="color: red"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="email"
                                                        name="email" placeholder="Your Email" maxlength="40" required>
                                                    <label for="email">Your Email*</label>
                                                    <span class="error-msg" id="error-email" style="color: red"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" maxlength="12"
                                                        id="mobile_number" name="mobile_number"
                                                        placeholder="Mobile number" required>
                                                    <label for="mobile_number">Mobile Number*</label>
                                                    <span class="error-msg" id="error-mobile_number"
                                                        style="color: red"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="subject"
                                                        name="subject" placeholder="Subject">
                                                    <label for="subject">Subject</label>
                                                    <span class="error-msg" id="error-subject"
                                                        style="color: red"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" placeholder="Leave a message here" id="message" name="message" style="height: 150px"></textarea>
                                                    <label for="message">Message*</label>
                                                    <span class="error-msg" id="error-message"
                                                        style="color: red"></span>
                                                </div>
                                            </div>
                                            <div class="col-12 text-center">
                                                <button id="contactSubmitBtn"
                                                    class="btn btn-primary-gradient rounded-pill fs-5 py-3 px-5"
                                                    type="submit">Schedule a Free Demo</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
