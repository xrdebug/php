target "docker-metadata-action" {}

target "build" {
  inherits = ["docker-metadata-action"]
  context = "./"
  dockerfile = "xr.Dockerfile"
  platforms = [
    "linux/amd64",
    "linux/arm64",
  ]
}
