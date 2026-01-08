                <form wire:submit.prevent="addToList">

                        <div class="d-flex justify-content-end gap-2 mt-3">
                      <button
                            type="submit"
                            class="btn btn-dark text-white position-relative"
                            wire:loading.attr="disabled"
                            wire:target="addToList">
                            {{-- Normal state --}}
                            <span wire:loading.remove wire:target="addToList">
                                  <i class="bi bi-check-circle me-1"></i>
                                  Add
                            </span>

                            {{-- Loading state --}}
                            <span wire:loading wire:target="addToList">
                                  <i class="bi bi-arrow-repeat spin me-1"></i>
                                  Adding...
                            </span>
                      </button>
                      </div>

                      <style>
                            .spin {
                                  display: inline-block;
                                  animation: spin 1s linear infinite;
                            }

                            @keyframes spin {
                                  0% {
                                        transform: rotate(0deg);
                                  }

                                  100% {
                                        transform: rotate(360deg);
                                  }
                            }
                      </style>
                </form>