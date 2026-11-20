# syntax=docker/dockerfile:1

# EasyRPG's buildscripts pin this emsdk version.
FROM emscripten/emsdk:5.0.5 AS build

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    autoconf automake build-essential bzip2 curl libtool meson ninja-build patch pkg-config unzip xz-utils \
  && rm -rf /var/lib/apt/lists/*

# The libraries change rarely, so they get their own cached layer.
# The buildscripts clone liblcf from its default branch.
ARG BUILDSCRIPTS_REF=84cf2f449a0d1c09f17d2bbf3d28f4f573aa718a
ARG LIBLCF_REF=6854310c3432e553fd4ae672ce861899c80c3bd0
WORKDIR /buildscripts
RUN git init -q \
  && git fetch -q --depth 1 https://github.com/EasyRPG/buildscripts.git "$BUILDSCRIPTS_REF" \
  && git checkout -q FETCH_HEAD \
  && cd emscripten \
  && export BUILD_LIBLCF=1 \
  && ./1_download_library.sh \
  && git -C liblcf checkout -q "$LIBLCF_REF" \
  && ./2_build_toolchain.sh \
  && ./3_cleanup.sh

# TODO: Point back at EasyRPG/Player once EasyRPG/Player#3643 merges.
ARG PLAYER_REPO=https://github.com/johannschopplich/EasyRPG-Player-fork.git
ARG PLAYER_REF=4df327e009e27cb26ec2bc940a33f15328a28a3b
WORKDIR /player
RUN git init -q \
  && git fetch -q --depth 1 "$PLAYER_REPO" "$PLAYER_REF" \
  && git checkout -q FETCH_HEAD \
  && emcmake cmake -B build -G Ninja \
    -DCMAKE_BUILD_TYPE=Release \
    -DCMAKE_PREFIX_PATH=/buildscripts/emscripten \
    -DCMAKE_FIND_ROOT_PATH_MODE_LIBRARY=BOTH \
    -DCMAKE_FIND_ROOT_PATH_MODE_INCLUDE=BOTH \
    -DCMAKE_FIND_ROOT_PATH_MODE_PACKAGE=BOTH \
    -DPLAYER_JS_OUTPUT_NAME=index \
  && cmake --build build

FROM scratch
COPY --from=build --chmod=644 /player/build/index.js /player/build/index.wasm /
